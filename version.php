<?php

require_once __DIR__ . "/vendor/autoload.php";

use Gharar\MoodleModGharar\Util;

Util::forbidNonMoodleAccess();

/*
 * The release version follows semantic versioning, and the version number (i.e.
 * in the form of YYYYMMDDXX) follows it, as described here.
 *
 * With every major or minor change (e.g. going from 0.1.0 to 0.2.0), the date
 * part of the version number (i.e. YYYYMMDD) is changed. With a bug-fix
 * release, the incremental part (i.e. XX) is then increased. It is highly
 * unlikely to happen, but if it reaches its limit (i.e. 99) and we are going
 * to have yet another bug-fix release, then we increase the minor part of the
 * release, and update the version number as described.
 */
$plugin->component = "mod_gharar";
$plugin->version = 2021102600;
$plugin->release = "0.4.0-alpha.1";
$plugin->maturity = MATURITY_ALPHA;

// Minimum Moodle version is 3.9.0
$plugin->requires = 2020061500;
$plugin->supported = [39, 311];
