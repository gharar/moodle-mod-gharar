<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Visual\Traits;

use MAChitgarha\MoodleModGharar\PageBuilding\Traits\{
    PageBuilderTrait as BasePageBuilderTrait
};

trait PageBuilderTrait
{
    use BasePageBuilderTrait {
        build as baseBuild
    }

    /** @var string */
    private $output = "";

    public function build(): self
    {
        return $this
            ->baseBuild()
            ->makeOutput();
    }

    protected function makeOutput(): self
    {
        $renderer = Util::getPageRenderer();

        $this->output .= $renderer->header();
        $this->output .= $renderer->heading($this->generateOutputHeading());
        $this->output .= $this->generateOutputMainContent();
        $this->output .= $renderer->footer();

        return $this;
    }

    abstract protected function generateOutputHeading(): string;
    abstract protected function generateOutputMainContent(): string;

    public function getOutput(): string
    {
        return $this->output;
    }
}
