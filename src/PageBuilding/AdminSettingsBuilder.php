<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use admin_root;
use admin_setting_configtext;
use admin_settingpage;
use MAChitgarha\MoodleModGharar\LanguageString\StringId;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\{Plugin, Util};

class AdminSettingsBuilder
{
    public const CONFIG_ACCESS_TOKEN_NAME = "access_token";

    /**
     * The root of the admin settings.
     * @var admin_root
     */
    private $root;

    /** @var admin_settingpage */
    private $mainPage;

    public function __construct(admin_settingpage $mainPage)
    {
        $this->root = Globals::getAdminSettings();
        $this->mainPage = $mainPage;
    }

    public function build(): self
    {
        if ($this->root->fulltree) {
            $this->addAccessTokenConfig();
        }

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
            self::generateFullConfigName(self::CONFIG_ACCESS_TOKEN_NAME),
            Util::getString(StringId::CONFIG_ACCESS_TOKEN),
            Util::getString(StringId::CONFIG_ACCESS_TOKEN_DESCRIPTION),
            /* default: */ 
            ""
        );

        $this->mainPage->add($config);

        return $this;
    }

    /**
     * Prepends the component name to the config name.
     */
    private static function generateFullConfigName(string $configName): string
    {
        return Plugin::COMPONENT_NAME . "/$configName";
    }
}
