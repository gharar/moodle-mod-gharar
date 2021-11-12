<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room;

class PossibleRoom
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
