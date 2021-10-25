<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use stdClass;
use cm_info;
use function \require_login;

trait RequireLoginTrait
{
    private function requireCourseLogin(int $courseId): self
    {
        require_login($courseId);

        return $this;
    }

    private function requireCourseModuleLogin(
        stdClass $course,
        cm_info $moduleInfo,
        bool $autoLoginGuest = true
    ): self {
        require_login($course, $autoLoginGuest, $moduleInfo);

        return $this;
    }
}
