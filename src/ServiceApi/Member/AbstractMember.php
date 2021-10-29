<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member;

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

    private function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setName(?string $name): self
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
