<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

trait PageBuilderTrait
{
    public function build(): self
    {
        return $this->configure();
    }

    abstract protected function configure(): self;
}
