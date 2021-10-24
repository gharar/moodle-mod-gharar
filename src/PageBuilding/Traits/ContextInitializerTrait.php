<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

trait ContextInitializerTrait
{
    /** @var context_module */
    private $context;

    private function initContext(int $instanceId): self
    {
        $this->context = \context_module::instance($instanceId);

        return $this;
    }
}
