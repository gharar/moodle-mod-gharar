<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use cm_info;
use function \require_login;

trait RequireLoginTrait
{
    private function requireCourseLogin(stdClass $course): self
    {
        require_login($this->course);

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
