<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

trait VisualPageBuilderTrait
{
    /** @var string */
    private $output = "";

    public function build(): self
    {
        $this
            ->configure()
            ->makeOutput();

        return $this;
    }

    abstract protected function configure();
    abstract protected function makeOutput();

    public function getOutput(): string
    {
        return $this->output;
    }
}
