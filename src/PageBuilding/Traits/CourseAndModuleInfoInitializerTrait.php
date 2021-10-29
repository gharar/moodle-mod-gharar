<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use cm_info;
use stdClass;

trait CourseAndModuleInfoInitializerTrait
{
    /** @var stdClass */
    private $course;

    /** @var cm_info */
    private $moduleInfo;

    private function initCourseAndModuleInfo(
        int $instanceId,
        string $moduleName
    ): self {
        [
            $this->course,
            $this->moduleInfo
        ] = \get_course_and_cm_from_cmid($instanceId, $moduleName);

        return $this;
    }
}
