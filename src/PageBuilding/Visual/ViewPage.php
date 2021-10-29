<?php

namespace Gharar\MoodleModGharar\PageBuilding\Visual;

use Gharar\MoodleModGharar\ServiceApi\Member\{
    AvailableLiveMember,
    AvailableRoomMember,
};
use Gharar\MoodleModGharar\ServiceApi\{
    AuthToken,
    Recording,
};
use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Redirect\{
    EnterLivePage,
    EnterRoomPage,
};
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\{
    Capability,
    Plugin,
    Util,
};
use moodle_url;

class ViewPage
{
    use Traits\TemplateBasedPageBuilderTrait;
    use BaseTraits\MoodleConfigLoaderTrait;
    use BaseTraits\CourseAndModuleInfoInitializerTrait;
    use BaseTraits\InstanceInitializerTrait;
    use BaseTraits\ApiInitializerTrait;
    use BaseTraits\RoomInfoInitializerTrait;
    use BaseTraits\ContextInitializerTrait;
    use BaseTraits\RequireLoginTrait {
        requireCourseModuleLogin as requireLogin;
    }

    public const RELATIVE_URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $instanceId;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo($this->instanceId, Plugin::MODULE_NAME)
            ->requireLogin($this->course, $this->moduleInfo)
            ->initInstance($this->moduleInfo)
            ->initModuleContext($this->instanceId)
            ->initApi()
            ->initRoomInfo($this->api, $this->instance->address);
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

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

    protected function generateOutputHeading(): string
    {
        // TODO: Append a "(live)" to the name if the room is live
        return $this->instance->name;
    }

    protected function getTemplateName(): string
    {
        return Plugin::COMPONENT_NAME . "/view";
    }

    protected function generateTemplateData(): array
    {
        return $this->generateRoomEntranceTemplateData()
            + $this->generateRecordingsTemplateData();
    }

    private function generateRoomEntranceTemplateData(): array
    {
        return [
            "section_enter_room_only" => !$this->roomInfo->hasLive(),
            "section_enter_room_and_live" => $this->roomInfo->hasLive(),

            "url_enter_room" => (new moodle_url(
                EnterRoomPage::RELATIVE_URL,
                ["id" => $this->instanceId]
            ))->out(),

            "url_enter_live" => (new moodle_url(
                EnterLivePage::RELATIVE_URL,
                ["id" => $this->instanceId]
            ))->out(),

            // Labels
            "enter_room" => Util::getString(StringId::PAGE_VIEW_ENTER_ROOM),
            "enter_room_having_live" => Util::getString(
                StringId::PAGE_VIEW_ENTER_ROOM_HAVING_LIVE
            ),
            "enter_live" => Util::getString(StringId::PAGE_VIEW_ENTER_LIVE),
        ];
    }

    private function generateRecordingsTemplateData(): array
    {
        return [
            "section_recordings" => $this->canUserViewRecordings(),

            "heading_recordings" => Util::getString(
                StringId::PAGE_VIEW_HEADING_RECORDINGS
            ),

            "list_recordings" => \array_map(
                function (Recording $recording) {
                    return [
                        "url" => $recording->getUrl(),
                        "name" => $recording->getName(),
                    ];
                },
                $this->api->listRoomRecordings($this->instance->address)
            ),

            "no_recordings_available" => Util::getString(
                StringId::PAGE_VIEW_NO_RECORDINGS_AVAILABLE
            ),
        ];
    }

    private function canUserViewRecordings(): bool
    {
        $userId = Globals::getUser()->id;
        $userRoles = \get_user_roles($this->moduleContext, $userId, true);

        foreach ($userRoles as $userRole) {
            if ($userRole->id === $this->instance->roles_can_view_recordings) {
                return true;
            }
        }

        if (\is_siteadmin($userId)) {
            return true;
        }

        return false;
    }
}
