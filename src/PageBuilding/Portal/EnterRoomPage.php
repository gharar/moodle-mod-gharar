<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal;

use Gharar\MoodleModGharar\ServiceApi\AuthToken;
use Gharar\MoodleModGharar\ServiceApi\Member\AvailableRoomMember;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\Traits as RootTraits;
use Gharar\MoodleModGharar\{
    Capability,
    Plugin,
    Util
};
use stdClass;

class EnterRoomPage
{
    use Traits\PageBuilder;
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
            ->initRoomInfo($this->api, $this->instance->address);
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    protected function prepare(): self
    {
        // TODO: See if the room is active or not.

        $this->ensurePresentUpdatedRoomMember(
            $roomMember = $this->generateRoomMember(
                Globals::getUser()
            )
        );

        $this->authToken = $this->api->generateAuthToken($roomMember);

        return $this;
    }

    private function generateRoomMember(stdClass $user): AvailableRoomMember
    {
        $roomMember = new AvailableRoomMember(
            Util::generateVirtualPhoneNumberFromId($user->id),
            $this->isCurrentUserRoomAdmin()
        );
        $roomMember->setName(
            "{$user->firstname} {$user->lastname}"
        );

        return $roomMember;
    }

    private function isCurrentUserRoomAdmin(): bool
    {
        return \has_capability(Capability::ROOM_ADMIN, $this->moduleContext);
    }

    private function ensurePresentUpdatedRoomMember(
        AvailableRoomMember $roomMember
    ): self {
        if ($this->api->hasRoomMember(
            $this->instance->address,
            $roomMember->getPhone()
        )) {
            $roomMember = $this->api->updateRoomMember(
                $this->instance->address,
                $roomMember
            );
        } else {
            $roomMember = $this->api->createRoomMember(
                $this->instance->address,
                $roomMember
            );
        }

        return $this;
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
