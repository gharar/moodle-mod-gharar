<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

trait PageBuilder
{
    public function build(): self
    {
        return $this->configure();
    }

    abstract protected function configure(): self;
}
