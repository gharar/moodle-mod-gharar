<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use admin_root;
use admin_setting_configtext;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\Util;

class AdminSettingsBuilder
{
    private const CONFIG_ACCESS_TOKEN_NAME = "mod_gharar/access_token";

    /**
     * The root of the admin settings.
     * @var admin_root
     */
    private $settings;

    public function __construct()
    {
        $this->settings = Globals::getInstance()->getAdminSettings();
    }

    public function build(): self
    {
        if (!$this->settings->fulltree) {
            return $this;
        }

        $this->addAccessTokenConfig();

        return $this;
    }

    /**
     * @todo Make it required.
     * @todo Validate access token before storage (maybe by inheriting its
     * config class and overriding validate() method?).
     */
    private function addAccessTokenConfig(): self
    {
        $config = new admin_setting_configtext(
            self::CONFIG_ACCESS_TOKEN_NAME,
            Util::getString("access_token"),
            Util::getString("access_token_description"),
            /* default: */ ""
        );

        $this->settings->add($config);

        return $this;
    }
}
