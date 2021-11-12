<?php

namespace Gharar\MoodleModGharar\ServiceApi\Room\Traits;

trait Address
{
    /** @var string */
    private $address;

    private function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
