<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

/*
 * See version.php for more information.
 */
class VersionMapper
{
    public const VERSION_UNKNOWN = -1;

    public const VERSION_0_1 = 20210716;
    public const VERSION_0_2 = self::VERSION_UNKNOWN;

    public static function extractDateFromVersionNumber(
        int $version
    ): int {
        return (int)($version / 100);
    }
}
