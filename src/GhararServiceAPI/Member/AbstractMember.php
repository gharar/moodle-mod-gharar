<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Member;

abstract class AbstractMember
{
    public const PROP_PHONE = "phone";
    public const PROP_NAME = "name";

    /** @var string */
    private $phone;

    /** @var string|null */
    private $name = null;

    public function __construct(string $phone)
    {
        $this->setPhone($phone);
    }

    public static function fromRawObject(object $object): self
    {
        $member = new self(
            $object->{self::PROP_PHONE}
        );
        $member->setName($object->{self::PROP_NAME});

        return $member;
    }

    private function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
    private function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
