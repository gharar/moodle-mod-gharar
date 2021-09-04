<?php

require_once __DIR__ . "/vendor/autoload.php";

use MAChitgarha\MoodleModGharar\Util;
use MAChitgarha\MoodleModGharar\InstanceDataForm;

Util::forbidNonMoodleAccess();

final class mod_gharar_mod_form extends InstanceDataForm
{
}
