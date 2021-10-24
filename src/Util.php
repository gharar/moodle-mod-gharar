<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\Moodle\Globals;
use core_renderer;
use Webmozart\PathUtil\Path;

class Util
{
    public static function forbidNonMoodleAccess(): void
    {
        if (!\defined("\MOODLE_INTERNAL")) {
            exit("Access forbidden");
        }
    }

    public static function generateVirtualPhoneNumberFromId(string $id): string
    {
        return "090010" . \str_pad($id, 5, "0", \STR_PAD_LEFT);
    }

    /**
     * @param string|object|array $fields
     * @return string
     */
    public static function getString(
        string $id,
        $fields = null,
        bool $lazyLoad = false
    ): string {
        return \get_string($id, Plugin::COMPONENT_NAME, $fields, $lazyLoad);
    }

    /**
     * @todo Throw exceptions in the case of config name not found?
     * @return mixed|false
     */
    public static function getConfig(string $name)
    {
        return \get_config(Plugin::COMPONENT_NAME, $name);
    }

    public static function getPageRenderer(): core_renderer
    {
        return Globals::getPage()->get_renderer("core");
    }

    public static function generatePageUrl(string $pathRelativeToModule): string
    {
        return Path::join(
            Globals::getConfig()->wwwroot,
            Plugin::RELATIVE_PATH,
            $pathRelativeToModule
        );
    }
}
