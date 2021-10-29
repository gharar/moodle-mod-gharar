<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room;

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

    /**
     * @return static
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return static
     */
    public function setIsPrivate(bool $isPrivate)
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
        $this->assertPropertyIsNotNull($this->isPrivate, "isPrivate");
        return $this->isPrivate;
    }

    protected function assertPropertyIsNotNull(
        $propertyValue,
        string $propertyName
    ): void {
        Assert::notNull(
            $propertyValue,
            "Property '$propertyName' must not be null"
        );
    }
}
