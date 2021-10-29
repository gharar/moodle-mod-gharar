<?php

namespace Gharar\MoodleModGharar;

use Gharar\MoodleModGharar\PluginUpgrade\VersionMapper;

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
        // For the testing phase, i.e. when the next version number is unknown
        if ($targetVersion === VersionMapper::VERSION_UNKNOWN) {
            return;
        }

        upgrade_mod_savepoint(
            $state,
            (string)(VersionMapper::makeVersionNumberFromDate($targetVersion)),
            Plugin::MODULE_NAME
        );
    }

    public static function uninstall(): bool
    {
        return true;
    }
}
