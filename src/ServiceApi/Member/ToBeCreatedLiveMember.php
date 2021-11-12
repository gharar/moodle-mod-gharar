<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class ToBeCreatedLiveMember
{
    use Traits\Phone;
    use Traits\OptionalName;

    public function __construct(string $phone)
    {
        $this->setPhone($phone);
    }
}
