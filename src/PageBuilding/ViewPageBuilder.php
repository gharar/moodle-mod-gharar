<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use cm_info;
use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;

class ViewPageBuilder extends AbstractPageBuilder
{
    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $instanceId;

    /** @var \stdClass */
    private $course;
    /** @var cm_info */
    private $moduleInfo;

    /** @var object */
    private $instance;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInfo()
            ->initInstance()
            ->requireLogin();
    }

    private function initParams(): self
    {
        $this->instanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function initCourseAndModuleInfo(): self
    {
        [
            $this->course,
            $this->moduleInfo
        ] = \get_course_and_cm_from_cmid(
            $this->instanceId,
            Plugin::MODULE_NAME
        );

        return $this;
    }

    private function initInstance(): self
    {
        $this->instance = Globals::getInstance()
            ->getDatabase()
            ->get_record(
                Database::TABLE_MAIN,
                ["id" => $this->moduleInfo->instance],
                "*",
                \MUST_EXIST
            );

        return $this;
    }


    private function requireLogin(): self
    {
        \require_login($this->course, true, $this->moduleInfo);

        return $this;
    }

    protected function buildPage(): self
    {
        $page = Globals::getInstance()->getPage();

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
        $room = new AvailableRoom(
            $this->instance->name,
            $this->instance->address
        );

        return \html_writer::link(
            $room->getShareUrl(),
            Util::getString("enter_room"),
            ["target" => "_blank"]
        );
    }
}
