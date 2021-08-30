<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Moodle\Globals;

abstract class AbstractPageBuilder
{
    /** @var string */
    protected $output = "";

    public function build(): self
    {
        $this
            ->buildPage()
            ->buildOutput();

        return $this;
    }

    abstract protected function buildPage(): self;

    protected function buildOutput(): self
    {
        $output = Globals::getInstance()->getOutput();

        $this->output .= $output->header();
        $this->output .= $output->heading($this->generateOutputHeading());
        $this->output .= $this->generateOutputMainContent();
        $this->output .= $output->footer();

        return $this;
    }

    abstract protected function generateOutputHeading(): string;
    abstract protected function generateOutputMainContent(): string;

    public function getOutput(): string
    {
        return $this->output;
    }
}
