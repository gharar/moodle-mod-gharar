<?php

namespace Gharar\MoodleModGharar\Traits;

use Gharar\MoodleModGharar\GhararServiceAPI\API;
use Gharar\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use Gharar\MoodleModGharar\Util;

trait ApiBuilderTrait
{
    private function makeApi(): API
    {
        return new API(
            Util::getConfig(
                AdminSettingsBuilder::CONFIG_ACCESS_TOKEN_NAME
            )
        );
    }
}
