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
                try {
                    (new $upgraderClass())->upgrade();
                    $currentVersion = $targetVersion;
                } catch (\Throwable $e) {
                    // Unstable state, upgrade process failed
                    self::upgradeModuleSavepoint(false, $targetVersion);
                    throw $e;
                }

                // Tell plugin stable state to Moodle
                self::upgradeModuleSavepoint(true, $targetVersion);
            }
        }

        return true;
    }

    private static function upgradeModuleSavepoint(
        bool $state,
        int $targetVersion
    ): void {
        upgrade_mod_savepoint(
            $state,
            VersionMapper::makeVersionNumberFromDate($targetVersion),
            Plugin::MODULE_NAME
        );
    }

    public static function uninstall(): bool
    {
        return true;
    }
}
