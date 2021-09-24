<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Member;

class AvailableRoomMember extends AbstractMember implements IsAdminInterface
{
    use IsAdminTrait;

    public function __construct(string $phone, bool $isAdmin)
    {
        parent::__construct($phone);
        $this->setIsAdmin($isAdmin);
    }

    public static function fromRawObject(object $object): self
    {
        $member = new self(
            $object->{self::PROP_PHONE},
            $object->{self::PROP_IS_ADMIN}
        );
        $member->setName($object->{self::PROP_NAME});

        return $member;
    }
}
