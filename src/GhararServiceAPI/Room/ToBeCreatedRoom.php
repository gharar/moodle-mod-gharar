<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

/**
 * A room that is supposed to be created. It may or may not exist. Because of
 * this, it has no known address, so not possible to get its information.
 */
class ToBeCreatedRoom extends AbstractRoom
{
    public function __construct(string $name, bool $isPrivate)
    {
        parent::__construct($name);
        $this->isPrivate($isPrivate);
    }
}
