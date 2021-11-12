<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

class ToBeCreatedLiveMember
{
    use Traits\Phone {
        setPhone as private;
    }
    use Traits\OptionalName;

    public function __construct(string $phone)
    {
        $this->setPhone($phone);
    }
}
