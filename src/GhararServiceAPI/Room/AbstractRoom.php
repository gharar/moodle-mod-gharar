<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

use Webmozart\Assert\Assert;

abstract class AbstractRoom
{
    public const PROP_NAME = "name";
    public const PROP_IS_PRIVATE = "is_private";

    /** @var string */
    private $name;

    /** @var bool|null */
    private $isPrivate = null;

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isPrivate(): bool
    {
        $this->assertPropertyIsNotNull("isPrivate");
        return $this->isPrivate;
    }

    protected function assertPropertyIsNotNull(string $propertyName): void
    {
        Assert::notNull(
            $this->$propertyName,
            "Property '$propertyName' must not be null"
        );
    }
}
