<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\PageBuilding\ViewPageBuilder;

class IndexPageBuilder extends AbstractPageBuilder
{
    private const URL = Plugin::RELATIVE_PATH . "/index.php";

    /** @var int */
    private $courseId = null;

    /** @var \stdClass */
    private $course = null;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourse()
            ->requireLogin();
    }

    private function initParams(): self
    {
        $this->courseId = \required_param("id", \PARAM_INT);

        return $this;
    }

    private function initCourse(): self
    {
        $this->course = Globals::getInstance()
            ->getDatabase()
            ->get_record(
                "course",
                ["id" => $this->courseId],
                "*",
                \MUST_EXIST
            );

        return $this;
    }

    private function requireLogin(): self
    {
        \require_login($this->course);

        return $this;
    }

    protected function buildPage(): self
    {
        $page = Globals::getInstance()->getPage();

        $page->set_url(self::URL, ["id" => $courseId]);
        $page->set_title(
            "{$this->course->shortname} " .
            Util::getString("plugin_name_plural")
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);
        $page->set_pagelayout("incourse");

        $page->navbar->add($page->title, $page->url);

        return $this;
    }

    protected function generateOutputHeading(): string
    {
        return Util::getString("plugin_name_plural");
    }

    protected function generateOutputMainContent(): string
    {
        // TODO: Wrap it inside a renderable
        $table = new \html_table();
        $table->attributes["class"] = "generaltable mod_index";

        $table->head = [
            Util::getString("meeting_name"),
            Util::getString("meeting_link")
        ];
        $table->align = ["center", "center"];

        $instances = Globals::getInstance()
            ->getDatabase()
            ->get_records(
                Plugin::DATABASE_MAIN_TABLE_NAME,
                ["course" => $this->courseId]
            );

        foreach ($instances as $instance) {
            $table->data[] = [
                \html_writer::link(
                    new \moodle_url(
                        ViewPageBuilder::URL,
                        ["id" => $this->courseId]
                    ),
                    $instance->name
                ),
                $instance->link,
            ];
        }

        return \html_writer::table($table);
    }
}
