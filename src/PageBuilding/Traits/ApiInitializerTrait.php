<?php

namespace Gharar\MoodleModGharar\PageBuilding\Traits;

use Gharar\MoodleModGharar\ServiceApi\Api;
use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\Util;

trait ApiInitializerTrait
{
    /** @var API */
    private $api;

    private function initApi(): self
    {
        $this->api = new Api(
            Util::getConfig(AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME)
        );

        return $this;
    }
}
