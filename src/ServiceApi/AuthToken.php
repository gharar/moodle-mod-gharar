<?php

namespace Gharar\MoodleModGharar\ServiceApi;

class AuthToken
{
    /** @var string */
    private $token;

    public function __construct(string $token)
    {
        $this->setToken($token);
    }

    private function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
