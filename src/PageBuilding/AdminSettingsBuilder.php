<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding;

use admin_root;
use admin_category;
use admin_settingpage;
use admin_setting_configtext;
use MAChitgarha\MoodleModGharar\Moodle\Globals;
use MAChitgarha\MoodleModGharar\Util;

class AdminSettingsBuilder
{
    private const CATEGORY_MAIN_NAME = "mod_gharar_main_settings_cat";
    private const CATEGORY_MAIN_PARENT_NAME = "modsettings";

    private const PAGE_MAIN_NAME = "mod_gharar";
    private const PAGE_MAIN_PARENT_NAME = self::CATEGORY_MAIN_NAME;

    public const CONFIG_ACCESS_TOKEN_NAME = "mod_gharar/access_token";

    /**
     * The root of the admin settings.
     * @var admin_root
     */
    private $root;

    /** @var admin_settingpage */
    private $mainPage;

    public function __construct()
    {
        $this->root = Globals::getInstance()->getAdminSettings();
    }

    public function build(): self
    {
        $this
            ->buildMainCategory()
            ->initMainPage();

        if ($this->root->fulltree) {
            $this->addAccessTokenConfig();
        }

        $this->addMainPage();

        return $this;
    }

    /**
     * Category is a set of related settings.
     */
    private function buildMainCategory(): self
    {
        $mainCategory = new admin_category(
            self::CATEGORY_MAIN_NAME,
            Util::getString("plugin_name")
        );
        $this->root->add(
            self::CATEGORY_MAIN_PARENT_NAME,
            $mainCategory
        );
        return $this;
    }

    private function initMainPage(): self
    {
        $this->mainPage = new admin_settingpage(
            self::PAGE_MAIN_NAME,
            Util::getString("plugin_name")
        );
        return $this;
    }

    private function addMainPage(): self
    {
        $this->root->add(
            self::PAGE_MAIN_PARENT_NAME,
            $this->mainPage
        );
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

        $this->mainPage->add($config);

        return $this;
    }
}
