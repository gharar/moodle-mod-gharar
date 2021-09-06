<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

class ViewPageBuilder extends AbstractPageBuilder
{
    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $moduleInstanceId;

    /** @var \stdClass */
    private $course;
    /** @var \cm_info */
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
        return \html_writer::link(
            $this->moduleInstance->link,
            Util::getString("enter_meeting_link"),
            ["target" => "_blank"]
        );
    }
}
