<?php

namespace Gharar\MoodleModGharar\PageBuilding\Visual\Traits;

use Gharar\MoodleModGharar\Util;

trait TemplateBasedPageBuilder
{
    use PageBuilder;

    protected function generateOutputMainContent(): string
    {
        return Util::getPageRenderer()
            ->render_from_template(
                $this->getTemplateName(),
                $this->generateTemplateData()
            );
    }

    abstract protected function getTemplateName(): string;
    abstract protected function generateTemplateData(): array;
}
