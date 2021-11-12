<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class AvailableLiveMember implements
    Interfaces\AvailableMember
{
    use Traits\Phone;
    use Traits\OptionalName;

    public function __construct(string $phone)
    {
        $this->setPhone($phone);
    }
}
