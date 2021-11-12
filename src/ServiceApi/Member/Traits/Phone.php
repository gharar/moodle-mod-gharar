<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member\Traits;

trait Phone
{
    /** @var string */
    private $phone;

    private function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
