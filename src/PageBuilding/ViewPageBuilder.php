<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use cm_info;
use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;

class ViewPageBuilder extends AbstractPageBuilder
{
    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $moduleInstanceId;

    /** @var \stdClass */
    private $course;
    /** @var cm_info */
    private $moduleInstance;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndModuleInstance()
            ->requireLogin();
    }

    private function initParams(): self
    {
        $this->moduleInstanceId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function initCourseAndModuleInstance(): self
    {
        [
            $this->course,
            $this->moduleInstance
        ] = \get_course_and_cm_from_cmid(
            $this->moduleInstanceId,
            Plugin::MODULE_NAME
        );

        return $this;
    }

    private function requireLogin(): self
    {
        \require_login($this->course, true, $this->courseModule);

        return $this;
    }

    protected function buildPage(): self
    {
        $page = Globals::getInstance()->getPage();

        $page->set_url(self::URL, ["id" => $this->moduleInstanceId]);
        $page->set_title(
            "{$this->course->shortname}: {$this->moduleInstance->name}"
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);

        return $this;
    }

    protected function generateOutputHeading(): string
    {
        return $this->moduleInstance->name;
    }

    protected function generateOutputMainContent(): string
    {
        $room = new AvailableRoom(
            $this->moduleInstance->name,
            $this->moduleInstance->address
        );

        return \html_writer::link(
            $room->getShareUrl(),
            Util::getString("enter_room"),
            ["target" => "_blank"]
        );
    }
}
