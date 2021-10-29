<?php

namespace Gharar\MoodleModGharar\Traits;

use Gharar\MoodleModGharar\ServiceApi\Api;
use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\Util;

trait ApiBuilderTrait
{
    private function makeApi(): API
    {
        return new Api(
            Util::getConfig(
                AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME
            )
        );
    }
}
