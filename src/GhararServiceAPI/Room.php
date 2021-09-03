<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

class Room
{
    /** @var string */
    private $name;

    /** @var bool */
    private $isPrivate;

    /** @var string|null */
    private $address = null;

    /** @var string|null */
    private $shareUrl = null;

    public function __construct(
        string $name,
        bool $isPrivate
    ) {
        $this
            ->setName($name)
            ->setIsPrivate($isPrivate);
    }

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

    public function getName(): string
    {
        return $this->name;
    }

    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function getAddress(): string
    {
        $this->assertPropertyIsNotNull("address");
        return $this->address;
    }

    public function getShareUrl(): string
    {
        if ($this->shareUrl !== null) {
            return $this->shareUrl;
        }
        return "https://room.gharar.ir/" . $this->getAddress();
    }

    private function assertPropertyIsNotNull(string $propertyName): void
    {
        if ($this->$propertyName === null) {
            throw new \Exception(
                "Property '$propertyName' must not be null"
            );
        }
    }
}
