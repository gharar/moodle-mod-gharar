<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

use Webmozart\Assert\Assert;

/**
 * A room that is supposed to be available and exist. In other words, you can
 * retrieve its information using {@link API::retrieveRoom()}.
 */
class AvailableRoom extends AbstractRoom
{
    public const ADDRESS = "address";
    public const SHARE_URL = "share_url";
    public const IS_ACTIVE = "is_active";

    /** @var string */
    private $address;

    /** @var string|null */
    private $shareUrl = null;

    /** @var bool */
    private $isActive;

    public function __construct(
        string $name,
        bool $isPrivate,
        string $address
    ) {
        parent::__construct($name, $isPrivate);
        $this->setAddress($address);
    }

    /**
     * @param object $object The decoded JSON, grabbed from the API call.
     */
    public static function fromRawObject(object $object): self
    {
        $room = new self(
            $object->{self::NAME},
            $object->{self::IS_PRIVATE},
            $object->{self::ADDRESS}
        );

        $room
            ->setShareUrl($object->{self::SHARE_URL})
            ->setIsActive($object->{self::IS_ACTIVE});

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
        return $this->isActive ?? true;
    }

    private function assertPropertyIsNotNull(string $propertyName): void
    {
        Assert::notNull(
            $this->$propertyName,
            "Property '$propertyName' must not be null"
        );
    }
}
