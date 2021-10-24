<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use cm_info;
use html_writer;
use context_module;
use MAChitgarha\MoodleModGharar\Capability;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableLiveMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableRoomMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\AuthToken;
use MAChitgarha\MoodleModGharar\PageBuilding\Traits\{
    VisualPageBuilderTrait,
    VisualPageOutputMakerTrait,
    CourseAndModuleInfoInitializerTrait,
    InstanceInitializerTrait,
    ContextInitializerTrait,
    ApiInitializerTrait,
};
use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;

class ViewPageBuilder extends AbstractPageBuilder
{
    use VisualPageBuilderTrait,
        VisualPageOutputMakerTrait,
        CourseAndModuleInfoInitializerTrait,
        InstanceInitializerTrait,
        ContextInitializerTrait,
        ApiInitializerTrait;

    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $instanceId;

    /** @var AvailableRoom */
    private $roomInfo;

    /** @var AuthToken */
    private $authToken;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo($this->instanceId, Plugin::MODULE_NAME)
            ->initInstance($this->moduleInfo)
            ->initContext($this->instanceId)
            ->requireLogin()
            ->initApi()
            ->initRoomInfo();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function requireLogin(): self
    {
        \require_login($this->course, true, $this->moduleInfo);

        return $this;
    }

    private function initRoomInfo(): self
    {
        $this->roomInfo = $this->api->retrieveRoom(
            $this->instance->address
        );

        return $this;
    }

    private function isCurrentUserRoomAdmin(): bool
    {
        return has_capability(
            Capability::ROOM_ADMIN,
            $this->context
        );
    }

    private function isCurrentRoomHavingLive(): bool
    {
        return $this->roomInfo->hasLive();
    }

    private function isCurrentUserNonAdminHavingLive(): bool
    {
        return
            $this->isCurrentRoomHavingLive() &&
            !$this->isCurrentUserRoomAdmin();
    }

    protected function configure(): self
    {
        $page = Globals::getPage();

        $page->set_url(self::URL, ["id" => $this->instanceId]);
        $page->set_title(
            "{$this->course->shortname}: {$this->instance->name}"
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);

        return $this;
    }

    protected function generateOutputHeading(): string
    {
        return $this->instance->name;
    }

    protected function generateOutputMainContent(): string
    {
        if (!$this->isCurrentRoomHavingLive()) {
            $url = implode([
                $this->roomInfo->getShareUrl(),
                "?jwt=",
                $this->authToken->getToken(),
            ]);
            $string = "enter_room";
        } else {
            $url = implode([
                $this->roomInfo->getLiveUrl(),
                "?token=",
                $this->authToken->getToken(),
            ]);
            $string = "enter_live";
        }

        return html_writer::link(
            $url,
            Util::getString($string),
            ["target" => "_blank"]
        );
    }
}
