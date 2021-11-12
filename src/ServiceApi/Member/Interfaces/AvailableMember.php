<?php

namespace Gharar\MoodleModGharar\ServiceApi\Member\Interfaces;

interface AvailableMember
{
    public function getPhone(): string;
    public function getName(): ?string;
}
