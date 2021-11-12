<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room;

/**
 * A room that is supposed to be created. It may or may not exist. Because of
 * this, it has no known address, so not possible to get its information.
 */
class ToBeCreatedRoom
{
    use Traits\Name;
    use Traits\IsPrivate;

    public function __construct(string $name, bool $isPrivate)
    {
        $this
            ->setName($name)
            ->setIsPrivate($isPrivate);
    }
}
