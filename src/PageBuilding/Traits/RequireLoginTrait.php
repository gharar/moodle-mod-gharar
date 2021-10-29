<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use cm_info;
use stdClass;

trait RequireLoginTrait
{
    private function requireCourseLogin(int $courseId): self
    {
        \require_login($courseId);

        return $this;
    }

    private function requireCourseModuleLogin(
        stdClass $course,
        cm_info $moduleInfo,
        bool $autoLoginGuest = true
    ): self {
        \require_login($course, $autoLoginGuest, $moduleInfo);

        return $this;
    }
}
