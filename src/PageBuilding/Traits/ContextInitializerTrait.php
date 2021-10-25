<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use context_module;
use context_course;

trait ContextInitializerTrait
{
    /** @var context_module */
    private $moduleContext;

    /** @var context_course */
    private $courseContext;

    private function initModuleContext(int $instanceId): self
    {
        $this->moduleContext = context_module::instance($instanceId);

        return $this;
    }

    private function initCourseContext(int $courseId): self
    {
        $this->courseContext = context_course::instance($courseId);

        return $this;
    }
}
