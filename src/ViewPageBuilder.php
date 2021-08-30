<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;

class ViewPageBuilder
{
    private const URL = "/mod/" . Plugin::MODULE_NAME . "/view.php";

    /** @var int */
    private $courseModuleId = null;

    /** @var \stdClass */
    private $course = null;
    /** @var \cm_info */
    private $courseModule = null;

    /** @var object */
    private $moduleInstance = null;

    /** @var string */
    private $output = "";

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

    public function build(): self
    {
        $this
            ->buildPage()
            ->buildOutput();

        return $this;
    }

    private function buildPage(): self
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

    private function buildOutput(): self
    {
        $output = Globals::getInstance()->getOutput();

        $this->output .= $output->header();
        $this->output .= $output->heading($this->moduleInstance->name);
        $this->output .= $this->generateMainContent();
        $this->output .= $output->footer();

        return $this;
    }

    private function generateMainContent(): string
    {
        return \html_writer::link(
            $this->moduleInstance->link,
            Util::getString('enter_meeting_link')
        );
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
