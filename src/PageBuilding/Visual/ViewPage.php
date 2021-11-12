<?php

namespace Gharar\MoodleModGharar\PageBuilding\Visual;

use Gharar\MoodleModGharar\Capability;
use Gharar\MoodleModGharar\ServiceApi\Recording;
use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Portal\{
    EnterLivePage,
    EnterRoomPage,
};
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\Traits as RootTraits;
use Gharar\MoodleModGharar\{
    Plugin,
    Util,
};
use moodle_url;

class ViewPage
{
    use Traits\TemplateBasedPageBuilder;
    use BaseTraits\MoodleConfigLoader;
    use BaseTraits\CourseAndModuleInfoInitializer;
    use BaseTraits\ApiInitializer;
    use BaseTraits\RoomInfoInitializer;
    use BaseTraits\ContextInitializer;
    use BaseTraits\RequireLogin {
        requireCourseModuleLogin as requireLogin;
    }
    use RootTraits\InstanceInitializer;

    public const RELATIVE_URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $instanceId;

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
        return \array_merge(
            $this->generatePortalLinksTemplateData(),
            $this->generateRecordingsTemplateData()
        );
    }

    private function generatePortalLinksTemplateData(): array
    {
        $templateData = \array_merge(
            $this->generatePortalLinksVisibilityTemplateData(),
            $this->generatePortalLinksUrlTemplateData()
        );

        $templateData = \array_merge(
            $templateData,
            $this->generatePortalLinksLabelTemplateData($templateData)
        );

        return $templateData;
    }

    private function generatePortalLinksVisibilityTemplateData(): array
    {
        if ($this->roomInfo->hasLive()) {
            return [
                "section_enter_room" => \has_capability(
                    Capability::LIVE_PRESENTER,
                    $this->moduleContext
                ),
                "section_enter_live" => true,
            ];
        } else {
            return [
                "section_enter_room" => true,
                "section_enter_live" => false,
            ];
        }
    }

    private function generatePortalLinksUrlTemplateData(): array
    {
        return [
            "url_enter_room" => (new moodle_url(
                EnterRoomPage::RELATIVE_URL,
                ["id" => $this->instanceId]
            ))->out(),
            "url_enter_live" => (new moodle_url(
                EnterLivePage::RELATIVE_URL,
                ["id" => $this->instanceId]
            ))->out(),
        ];
    }

    private function generatePortalLinksLabelTemplateData(
        array $currentTemplateData
    ): array {
        if (
            $currentTemplateData["section_enter_room"] &&
            $currentTemplateData["section_enter_live"]
        ) {
            return [
                "enter_room" => Util::getString(StringId::PAGE_VIEW_ENTER_ROOM),
                "enter_live" => Util::getString(StringId::PAGE_VIEW_ENTER_LIVE),
            ];
        } elseif ($currentTemplateData["section_enter_room"]) {
            return ["enter_room" => Util::getString(StringId::PAGE_VIEW_ENTER)];
        } elseif ($currentTemplateData["section_enter_live"]) {
            return ["enter_live" => Util::getString(StringId::PAGE_VIEW_ENTER)];
        }

        // TODO: Set a message for it! :)
        throw new \LogicException();
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
            if (\in_array(
                $userRole->id,
                $this->instance->roles_can_view_recordings
            )) {
                return true;
            }
        }

        if (\is_siteadmin($userId)) {
            return true;
        }

        return false;
    }
}
