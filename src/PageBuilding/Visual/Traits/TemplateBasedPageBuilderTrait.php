<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Visual\Traits;

use MAChitgarha\MoodleModGharar\Util;

trait TemplateBasedPageBuilderTrait
{
    use PageBuilderTrait;

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