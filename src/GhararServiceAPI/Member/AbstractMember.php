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

    /**
     * @return static
     */
    public static function fromRawObject(object $object)
    {
        $member = new static(
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
