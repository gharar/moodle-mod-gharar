<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room;

/**
 * A room that is supposed to be available and exist. In other words, you can
 * retrieve its information using {@link Api::retrieveRoom()}.
 */
class AvailableRoom extends AbstractRoom
{
    public const PROP_ADDRESS = "address";
    public const PROP_IS_ACTIVE = "is_active";
    public const PROP_HAS_LIVE = "has_live";
    public const PROP_LIVE_URL = "live_url";

    /** @var string */
    private $address;

    /** @var bool|null */
    private $isActive = null;

    /** @var bool|null */
    private $hasLive = null;

    /** @var string|null */
    private $liveUrl = null;

    public function __construct(string $name, string $address)
    {
        parent::__construct($name);
        $this->setAddress($address);
    }

    /**
     * @param object $object The decoded JSON, grabbed from the API call.
     */
    public static function fromRawObject(object $object): self
    {
        $room = new self(
            $object->{self::PROP_NAME},
            $object->{self::PROP_ADDRESS}
        );

        $room
            ->setIsPrivate($object->{self::PROP_IS_PRIVATE})
            ->setIsActive($object->{self::PROP_IS_ACTIVE})
            ->setHasLive($object->{self::PROP_HAS_LIVE})
            ->setLiveUrl($object->{self::PROP_LIVE_URL});

        return $room;
    }

    private function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
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

    public function getAddress(): string
    {
        return $this->address;
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
}
