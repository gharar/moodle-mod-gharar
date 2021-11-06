<?php

namespace Gharar\MoodleModGharar\PageBuilding\Portal\Traits;

use Gharar\MoodleModGharar\PageBuilding\Traits\{
    PageBuilderTrait as BasePageBuilderTrait
};

trait PageBuilderTrait
{
    use BasePageBuilderTrait {
        build as baseBuild;
    }

    public function build(): self
    {
        return $this
            ->baseBuild()
            ->prepare();
    }

    abstract protected function prepare(): self;

    public function redirect(): void
    {
        \header("Location: " . $this->generateRedirectionLocation());
    }

    abstract protected function generateRedirectionLocation(): string;
}
