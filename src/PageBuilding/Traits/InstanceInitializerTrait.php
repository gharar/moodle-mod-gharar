<?php

namespace MAChitgarha\MoodleModGharar\PageBuilding\Traits;

use cm_info;
use MAChitgarha\MoodleModGharar\Database;
use MAChitgarha\MoodleModGharar\Moodle\Globals;

trait InstanceInitializerTrait
{
    /** @var object */
    private $instance;

    private function initInstance(cm_info $moduleInfo): self
    {
        $this->instance = Globals::getDatabase()
            ->get_record(
                Database::TABLE_MAIN,
                ["id" => $moduleInfo->instance],
                "*",
                \MUST_EXIST
            );

        return $this;
    }
}
