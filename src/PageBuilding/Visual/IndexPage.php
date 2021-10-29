<?php

namespace Gharar\MoodleModGharar\PageBuilding\Visual;

use Gharar\MoodleModGharar\LanguageString\StringId;
use Gharar\MoodleModGharar\Moodle\Globals;
use Gharar\MoodleModGharar\PageBuilding\Traits as BaseTraits;
use Gharar\MoodleModGharar\{
    Database,
    Plugin,
    Util
};
use stdClass;

class IndexPage
{
    use Traits\TemplateBasedPageBuilderTrait;
    use BaseTraits\MoodleConfigLoaderTrait;

    use BaseTraits\RequireLoginTrait {
        requireCourseLogin as requireLogin;
    }

    private const RELATIVE_URL = Plugin::RELATIVE_PATH . "/index.php";

    /** @var int */
    private $courseId;

    /** @var \stdClass */
    private $course;

    public function __construct()
    {
        $this
            ->initParams()
            ->initCourse()
            ->requireLogin($this->courseId);
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

    protected function configure(): self
    {
        $page = Globals::getPage();

        $page->set_url(self::RELATIVE_URL, ["id" => $this->courseId]);
        $page->set_title(
            "{$this->course->shortname} " .
            Util::getString(StringId::PLUGIN_NAME_PLURAL)
        );
        $page->set_heading($this->course->fullname);
        $page->set_cacheable(false);
        $page->set_pagelayout("incourse");

        $page->navbar->add($page->title, $page->url);

        return $this;
    }

    protected function generateOutputHeading(): string
    {
        return Util::getString(StringId::PLUGIN_NAME_PLURAL);
    }

    protected function getTemplateName(): string
    {
        return Plugin::COMPONENT_NAME . "/index";
    }

    protected function generateTemplateData(): array
    {
        return [
            "name" =>
                Util::getString(StringId::FORM_INSTANCE_FIELD_NAME),
            "room_name" =>
                Util::getString(StringId::FORM_INSTANCE_FIELD_ROOM_NAME),

            "records" => \array_values(\array_map(
                function (stdClass $instance) {
                    return [
                        "name" => $instance->name,
                        "room_name" => $instance->room_name,
                    ];
                },
                Globals::getDatabase()->get_records(
                    Database\Table::MAIN,
                    ["course" => $this->courseId]
                )
            )),
        ];
    }
}
