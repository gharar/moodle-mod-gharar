<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\ServiceApi\Member\{
    AvailableRoomMember,
    PossibleRoomMember,
};
use Gharar\MoodleModGharar\ServiceApi\{
    AuthToken,
    Member,
};
use Gharar\MoodleModGharar\Traits as RootTraits;
use Gharar\MoodleModGharar\{
    Capability,
    Plugin,
    Util,
};
use required_capability_exception;
use stdClass;

class EnterRoomPage
{
    use Traits\PageBuilder;
    use Traits\RoomIsActiveAssertion;
    use BaseTraits\MoodleConfigLoader;
    use BaseTraits\CourseAndModuleInfoInitializer;
    use BaseTraits\ContextInitializer;
    use BaseTraits\RoomInfoInitializer;
    use BaseTraits\RequireLogin {
        requireCourseModuleLogin as requireLogin;
    }
    use RootTraits\ApiInitializer;
    use RootTraits\InstanceInitializer;

    public const RELATIVE_URL = Base::RELATIVE_PATH . "/enter-room.php";

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
            ->initModuleContext($this->instanceId)
            ->initApi()
            ->initRoomInfo($this->api, $this->instance->address)
            ->assertRoomIsActive($this->roomInfo)
            ->assertUserCanBePresenterIfRoomHavingLive();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function assertUserCanBePresenterIfRoomHavingLive(): self
    {
        if ($this->roomInfo->hasLive() && !\has_capability(
            Capability::LIVE_PRESENTER,
            $this->moduleContext
        )) {
            throw new required_capability_exception(
                $this->moduleContext,
                Capability::LIVE_PRESENTER,
                Util::getString(StringId::ERROR_ACCESS_DENIED),
                ""
            );
        }

        return $this;
    }

    protected function prepare(): self
    {
        $roomMember = $this->makeRoomMemberAvailableAndUpdated(
            $this->buildRoomMember(Globals::getUser())
        );

        $this->authToken = $this->api->generateAuthToken($roomMember);

        return $this;
    }

    private function buildRoomMember(stdClass $user): PossibleRoomMember
    {
        return (new PossibleRoomMember(
            Util::generateVirtualPhoneNumberFromId($user->id),
            $this->isCurrentUserRoomAdmin()
        ))
            ->setName("{$user->firstname} {$user->lastname}");
    }

    private function isCurrentUserRoomAdmin(): bool
    {
        return \has_capability(Capability::ROOM_ADMIN, $this->moduleContext);
    }

    private function makeRoomMemberAvailableAndUpdated(
        PossibleRoomMember $roomMember
    ): AvailableRoomMember {
        if (!$this->api->hasRoomMember(
            $this->instance->address,
            $roomMember->getPhone()
        )) {
            return $this->api->createRoomMember(
                $this->instance->address,
                $roomMember
            );
        } else {
            return $this->api->updateRoomMember(
                $this->instance->address,
                Member\Mapper::possibleRoomMemberToAvailableRoomMember(
                    $roomMember
                )
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
            $this->roomInfo->getShareUrl(),
            "?token=",
            $this->authToken->getToken(),
        ]);
    }
}
