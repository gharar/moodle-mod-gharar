<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use context_module;

trait ContextInitializerTrait
{
    /** @var context_module */
    private $context;

    private function initContext(int $instanceId): self
    {
        $this->context = context_module::instance($instanceId);

        return $this;
    }
}
