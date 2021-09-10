<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\Plugin;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

class IndexPageBuilder extends AbstractPageBuilder
{
    private const URL = Plugin::RELATIVE_PATH . "/index.php";

    /** @var int */
    private $courseId;

    /** @var \stdClass */
    private $course;

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
        $this->course = Globals::getDatabase()
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
        $page = Globals::getPage();

        $page->set_url(self::URL, ["id" => $this->courseId]);
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
            Util::getString("name"),
            Util::getString("room_name"),
        ];
        $table->align = ["center", "center"];

        $instances = Globals::getDatabase()
            ->get_records(
                Database::TABLE_MAIN,
                ["course" => $this->courseId]
            );

        foreach ($instances as $instance) {
            $table->data[] = [
                $instance->name,
                // TODO: Make it a link
                $instance->room_name
            ];
        }

        return \html_writer::table($table);
    }
}
