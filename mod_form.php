<?php

require_once __DIR__ . "/vendor/autoload.php";

use Gharar\MoodleModGharar\{
    InstanceForm,
    Util,
};

Util::forbidNonMoodleAccess();

final class mod_gharar_mod_form extends InstanceForm
{
}
