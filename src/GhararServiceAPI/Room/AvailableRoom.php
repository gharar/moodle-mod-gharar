<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

/**
 * A room that is supposed to be available and exist. In other words, you can
 * retrieve its information using {@link API::retrieveRoom()}.
 */
class AvailableRoom extends AbstractRoom
{
    public const PROP_ADDRESS = "address";
    public const PROP_SHARE_URL = "share_url";
    public const PROP_IS_ACTIVE = "is_active";

    /** @var string */
    private $address;

    /** @var string|null */
    private $shareUrl = null;

    /** @var bool|null */
    private $isActive = null;

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
            ->setShareUrl($object->{self::PROP_SHARE_URL})
            ->setIsActive($object->{self::PROP_IS_ACTIVE});

        return $room;
    }

    private function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    private function setShareUrl(string $shareUrl): self
    {
        $this->shareUrl = $shareUrl;
        return $this;
    }

    private function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getShareUrl(): string
    {
        return $this->shareUrl ??
            "https://room.gharar.ir/" . $this->getAddress();
    }

    public function isActive(): bool
    {
        $this->assertPropertyIsNotNull("isActive");
        return $this->isActive;
    }
}
