<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Visual;

use MAChitgarha\MoodleModGharar\PageBuilding\Traits\{
    MoodleConfigLoaderTrait,
    VisualPageBuilderTrait,
    VisualPageOutputMakerTrait,
    CourseAndModuleInfoInitializerTrait,
    InstanceInitializerTrait,
    ContextInitializerTrait,
    ApiInitializerTrait,
};
use cm_info;
use moodle_url;
use context_module;
use MAChitgarha\MoodleModGharar\Capability;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableLiveMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableRoomMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\AuthToken;
use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\LanguageString\StringId;

class ViewPage
{
    use MoodleConfigLoaderTrait,
        VisualPageBuilderTrait,
        VisualPageOutputMakerTrait,
        CourseAndModuleInfoInitializerTrait,
        InstanceInitializerTrait,
        ContextInitializerTrait,
        ApiInitializerTrait;

    public const URL = Plugin::RELATIVE_PATH . "/view.php";
    public const TEMPLATE_NAME = Plugin::COMPONENT_NAME . "/view";

    private const PAGE_RELATIVE_PATH_ENTER_ROOM =
        "pages/redirect/enter-room.php";
    private const PAGE_RELATIVE_PATH_ENTER_LIVE =
        "pages/redirect/enter-live.php";

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
        // TODO: Append a "(live)" to the name if the room is live
        return $this->instance->name;
    }

    protected function generateOutputMainContent(): string
    {
        return Util::getPageRenderer()
            ->render_from_template(
                self::TEMPLATE_NAME,
                $this->generateTemplateData()
            );
    }

    private function generateTemplateData(): array
    {
        return [
            "enter_room_only" => !$this->roomInfo->hasLive(),
            "enter_room_and_live" => $this->roomInfo->hasLive(),

            "enter_room" => Util::getString(StringId::PAGE_VIEW_ENTER_ROOM),
            "enter_room_having_live" => Util::getString(
                StringId::PAGE_VIEW_ENTER_ROOM_HAVING_LIVE
            ),
            "enter_live" => Util::getString(StringId::PAGE_VIEW_ENTER_LIVE),

            "enter_room_link" => Util::getPageUrl(
                self::PAGE_RELATIVE_PATH_ENTER_ROOM
            ),
            "enter_live_link" => Util::getPageUrl(
                self::PAGE_RELATIVE_PATH_ENTER_LIVE
            ),
        ];
    }
}
