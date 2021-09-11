<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\PluginUpgrade\VersionMapper;
use MAChitgarha\MoodleModGharar\PluginUpgrade\From0o1To0o2;

class PluginManager
{
    public static function upgrade(int $oldVersionNumber = 0): bool
    {
        $currentVersion = VersionMapper::extractDateFromVersionNumber(
            $oldVersionNumber
        );

        /*
         * Updates are incremental. Meaning, for example, if the plugin is being
         * upgraded from 0.1.0 to 0.3.0, it initially will be upgraded from
         * 0.2.0, and then upgraded to 0.3.0.
         */
        if ($currentVersion === VersionMapper::VERSION_0_1) {
            (new From0o1To0o2())->upgrade();
            $currentVersion = VersionMapper::VERSION_0_2;
        }

        return true;
    }

    public static function uninstall(): bool
    {
        return true;
    }
}
