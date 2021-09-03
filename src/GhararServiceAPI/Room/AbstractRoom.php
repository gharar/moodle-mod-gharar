<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

abstract class AbstractRoom
{
    /** @var string */
    public const NAME = "name";
    /** @var string */
    public const IS_PRIVATE = "is_private";

    /** @var string */
    private $name;

    /** @var bool */
    private $isPrivate;

    public function __construct(
        string $name,
        bool $isPrivate
    ) {
        $this
            ->setName($name)
            ->setIsPrivate($isPrivate);
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
        return $this->isPrivate;
    }
}
