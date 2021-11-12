<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room;

/**
 * A room that is supposed to be available and exist. In other words, you can
 * retrieve its information using {@link Api::retrieveRoom()}.
 * @todo Move all its individual properties to traits.
 */
class AvailableRoom
{
    use Traits\Name;
    use Traits\Address;
    use Traits\IsPrivate {
        setIsPrivate as public;
    }

    /** @var bool|null */
    private $isActive = null;

    /** @var bool|null */
    private $hasLive = null;

    /** @var string|null */
    private $liveUrl = null;

    public function __construct(string $name, string $address)
    {
        $this
            ->setName($name)
            ->setAddress($address);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setHasLive(bool $hasLive): self
    {
        $this->hasLive = $hasLive;
        return $this;
    }

    public function setLiveUrl(string $liveUrl): self
    {
        $this->liveUrl = $liveUrl;
        return $this;
    }

    public function getShareUrl(): string
    {
        return "https://gharar.ir/room/{$this->getAddress()}";
    }

    public function isActive(): bool
    {
        if ($this->isActive !== null) {
            return $this->isActive;
        }
        $this->throwPropertyIsNullException("isActive");
    }

    public function hasLive(): bool
    {
        if ($this->hasLive !== null) {
            return $this->hasLive;
        }
        $this->throwPropertyIsNullException("hasLive");
    }

    public function getLiveUrl(): string
    {
        if ($this->liveUrl !== null) {
            return $this->liveUrl;
        }
        $this->throwPropertyIsNullException("liveUrl");
    }

    /**
     * @todo Move it to a trait.
     * @return no-return
     */
    private function throwPropertyIsNullException(string $propertyName): void
    {
        throw new \UnexpectedValueException(
            "Property '$propertyName' must not be null"
        );
    }
}
