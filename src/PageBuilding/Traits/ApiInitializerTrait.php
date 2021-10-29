<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use Gharar\MoodleModGharar\GhararServiceAPI\API;
use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\Util;

trait ApiInitializerTrait
{
    /** @var API */
    private $api;

    private function initApi(): self
    {
        $this->api = new API(
            Util::getConfig(AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME)
        );

        return $this;
    }
}
