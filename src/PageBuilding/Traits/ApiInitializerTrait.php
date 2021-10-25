<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use MAChitgarha\MoodleModGharar\GhararServiceAPI\API;
use MAChitgarha\MoodleModGharar\PageBuilding\AdminSettingsBuilder;
use MAChitgarha\MoodleModGharar\Util;

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
