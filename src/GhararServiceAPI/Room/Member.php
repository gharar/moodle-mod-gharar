<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

class Member
{
    public const PROP_PHONE = "phone";
    public const PROP_IS_ADMIN = "is_admin";
    public const PROP_NAME = "name";

    /** @var string */
    private $phone;

    /** @var bool */
    private $isAdmin;

    /** @var string|null */
    private $name = null;

    public function __construct(string $phone, bool $isAdmin)
    {
        $this
            ->setPhone($phone)
            ->setIsAdmin($isAdmin);
    }

    public static function fromRawObject(object $object): self
    {
        $user = new self(
            $object->{self::PROP_PHONE},
            // TODO: Fix this
            $object->{self::PROP_IS_ADMIN} ?? false
        );

        $user->setName($object->{self::PROP_NAME});

        return $user;
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

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
