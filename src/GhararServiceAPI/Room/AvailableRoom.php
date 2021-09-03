<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI\Room;

use Webmozart\Assert\Assert;

/**
 * A room that is supposed to be available and exist. In other words, you can
 * retrieve its information using {@link API::retrieveRoom()}.
 */
class AvailableRoom extends AbstractRoom
{
    /** @var string */
    public const ADDRESS = "address";
    /** @var string */
    public const SHARE_URL = "share_url";
    /** @var string */
    public const IS_ACTIVE = "is_active";

    /** @var string|null */
    private $address = null;

    /** @var string|null */
    private $shareUrl = null;

    /** @var bool */
    private $isActive;

    public static function fromRawObject(object $object): self
    {
        $room = new self(
            $object->name,
            $object->is_private
        );

        $room
            ->setAddress($object->address)
            ->setShareUrl($object->share_url);

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
        $this->assertPropertyIsNotNull("address");
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
