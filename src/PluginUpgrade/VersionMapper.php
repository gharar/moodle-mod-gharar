<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

/*
 * See version.php for more information.
 */
class VersionMapper
{
    public const VERSION_UNKNOWN = -1;

    public const VERSION_0_1 = 20210716;
    public const VERSION_0_2 = 20210905;
    public const VERSION_0_3 = 20210924;
    public const VERSION_0_4 = self::VERSION_UNKNOWN;

    /**
     * Maps every version to the pair of (1) the class that does the upgrade to
     * the next version, and (2) the resulting version.
     *
     * This helps incremental updates being done easily, by iterating the array,
     * invoke the upgrade function of the related class and increase the version
     * up.
     *
     * For example, if the plugin is being upgraded from 0.1.0 to 0.3.0, it
     * initially should be upgraded to 0.2.0, and then to 0.3.0.
     */
    public const INCREMENTAL_UPGRADE_INFO = [
        VERSION_0_1 => [From0o1To0o2::class, VERSION_0_2],
        VERSION_0_2 => [StubUpgrader::class, VERSION_0_3],
    ];

    public static function extractDateFromVersionNumber(int $version): int
    {
        return (int)($version / 100);
    }
}
