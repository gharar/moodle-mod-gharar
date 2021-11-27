<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\ServiceApi\Member\{
    AvailableLiveMember,
    PossibleLiveMember,
};
use Gharar\MoodleModGharar\ServiceApi\{
    AuthToken,
    Member,
};
use Gharar\MoodleModGharar\Traits as RootTraits;
use Gharar\MoodleModGharar\{
    Plugin,
    Util,
};
use stdClass;

class EnterLivePage
{
    use Traits\PageBuilder;
    use Traits\RoomIsActiveAssertion;
    use BaseTraits\MoodleConfigLoader;
    use BaseTraits\CourseAndModuleInfoInitializer;
    use BaseTraits\RoomInfoInitializer;
    use BaseTraits\RequireLogin {
        requireCourseModuleLogin as requireLogin;
    }
    use RootTraits\ApiInitializer;
    use RootTraits\InstanceInitializer;

    public const RELATIVE_URL = Base::RELATIVE_PATH . "/enter-live.php";

    /** @var int */
    private $instanceId;

    /** @var AuthToken */
    private $authToken;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo($this->instanceId, Plugin::MODULE_NAME)
            ->requireLogin($this->course, $this->moduleInfo)
            ->initInstance($this->moduleInfo->instance)
            ->initApi()
            ->initRoomInfo($this->api, $this->instance->address)
            ->assertRoomIsActive($this->roomInfo)
            ->assertRoomHavingLive();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function assertRoomHavingLive(): self
    {
        if (!$this->roomInfo->hasLive()) {
            throw new \RuntimeException(StringId::ERROR_ROOM_HAS_NO_LIVE);
        }

        return $this;
    }

    protected function prepare(): self
    {
        $liveMember = $this->makeLiveMemberAvailable(
            $this->buildLiveMember(Globals::getUser())
        );

        $this->authToken = $this->api->generateAuthToken($liveMember);

        return $this;
    }

    private function buildLiveMember(stdClass $user): PossibleLiveMember
    {
        return (new PossibleLiveMember(
            Util::generateVirtualPhoneNumberFromId($user->id)
        ))
            ->setName("{$user->firstname} {$user->lastname}");
    }

    private function makeLiveMemberAvailable(
        PossibleLiveMember $liveMember
    ): AvailableLiveMember {
        if (!$this->api->hasLiveMember(
            $this->instance->address,
            $liveMember->getPhone()
        )) {
            return $this->api->createLiveMember(
                $this->instance->address,
                $liveMember
            );
        } else {
            return Member\Mapper::possibleLiveMemberToAvailableLiveMember(
                $liveMember
            );
        }
    }

    protected function configure(): self
    {
        $page = Globals::getPage();

        $page->set_url(self::RELATIVE_URL, ["id" => $this->instanceId]);
        $page->set_title(
            "{$this->course->shortname}: {$this->instance->name}"
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);

        return $this;
    }

    public function generateRedirectionLocation(): string
    {
        return \implode([
            $this->roomInfo->getLiveUrl(),
            "?token=",
            $this->authToken->getToken(),
        ]);
    }
}
