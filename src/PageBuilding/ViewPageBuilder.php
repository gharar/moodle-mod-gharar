<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Moodle\Globals;

class ViewPageBuilder extends AbstractPageBuilder
{
    public const URL = Plugin::RELATIVE_PATH . "/view.php";

    /** @var int */
    private $courseModuleId = null;

    /** @var \stdClass */
    private $course = null;
    /** @var \cm_info */
    private $courseModule = null;

    /** @var object */
    private $moduleInstance = null;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourseAndCourseModule()
            ->initModuleInstance()
            ->requireLogin();
    }

    private function initParams(): self
    {
        $this->courseModuleId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function initCourseAndCourseModule(): self
    {
        [
            $this->course,
            $this->courseModule,
        ] = \get_course_and_cm_from_cmid(
            $this->courseModuleId,
            Plugin::MODULE_NAME,
        );

        return $this;
    }

    private function initModuleInstance(): self
    {
        $this->moduleInstance = Globals::getInstance()
            ->getDatabase()
            ->get_record(
                Plugin::DATABASE_MAIN_TABLE_NAME,
                ["id" => $this->courseModule->instance],
                "*",
                \MUST_EXIST,
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

        $page->set_url(self::URL, ["id" => $this->courseModuleId]);
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
            Util::getString("enter_meeting_link")
        );
    }
}
