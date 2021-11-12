<?php

namespace Gharar\MoodleModGharar\Traits;

use Gharar\MoodleModGharar\Database;
use Gharar\MoodleModGharar\Util;
use Gharar\MoodleModGharar\Moodle\Globals;

trait InstanceInitializer
{
    /** @var object */
    private $instance;

    private function initInstance(int $instanceId): self
    {
        $this->instance = Globals::getDatabase()
            ->get_record(
                Database\Table::MAIN,
                ["id" => $instanceId],
                "*",
                \MUST_EXIST
            );

        $this->instance->roles_can_view_recordings = Util::jsonDecode(
            $this->instance->roles_can_view_recordings
        );

        return $this;
    }
}
