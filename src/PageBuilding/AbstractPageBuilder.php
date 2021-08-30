<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use MAChitgarha\MoodleModGharar\Moodle\Globals;

/*
 * Bootstrap include file.
 *
 * The config.php file is not limited to providing $CFG global (although the
 * Globals class should be used in this case), unlike its name suggests, but
 * also includes all required functions and classes as well, like the
 * well-known required_param() function.
 *
 * As why those parent directories in the path exists: The first two are
 * obvious, reaching the root of the plugin; so we must go up two more levels
 * to reach the Moodle root. The $CFG or Globals::getConfig() function cannot
 * be used either: They rely on the same file we are including.
 */
require_once __DIR__ . "/../../../../config.php";

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
