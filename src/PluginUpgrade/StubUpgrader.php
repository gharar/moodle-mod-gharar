<?php

namespace MAChitgarha\MoodleModGharar\PluginUpgrade;

class StubUpgrader extends AbstractBase
{
    public function upgrade(): void
    {
    }

    protected function upgradeMainTableRecord(\stdClass $record): array
    {
        return [true, $record];
    }
}
