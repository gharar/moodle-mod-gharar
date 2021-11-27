<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal\Traits;

use Gharar\MoodleModGharar\PageBuilding\Traits\{
    PageBuilder as BasePageBuilder
};

trait PageBuilder
{
    use BasePageBuilder {
        build as baseBuild;
    }

    public function build(): self
    {
        return $this
            ->prepare()
            ->baseBuild();
    }

    abstract protected function prepare(): self;

    public function redirect(): void
    {
        \header("Location: " . $this->generateRedirectionLocation());
    }

    abstract protected function generateRedirectionLocation(): string;
}
