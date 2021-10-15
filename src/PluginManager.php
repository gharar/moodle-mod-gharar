<?php

namespace MAChitgarha\MoodleModGharar;

use MAChitgarha\MoodleModGharar\PluginUpgrade\VersionMapper;

class PluginManager
{
    public static function upgrade(int $oldVersionNumber = 0): bool
    {
        $currentVersion = VersionMapper::extractDateFromVersionNumber(
            $oldVersionNumber
        );

        foreach (VersionMapper::INCREMENTAL_UPGRADE_INFO as $srcVersion => [
            $upgraderClass,
            $targetVersion,
        ]) {
            if ($currentVersion === $srcVersion) {
                (new $upgraderClass())->upgrade();
                $currentVersion = $targetVersion;
            }
        }

        return true;
    }

    public static function uninstall(): bool
    {
        return true;
    }
}
