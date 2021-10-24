<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use MAChitgarha\MoodleModGharar\Util;

/**
 * Extends VisualPageBuilderTrait.
 */
trait VisualPageOutputMakerTrait
{
    private function makeOutput(): self
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
}
