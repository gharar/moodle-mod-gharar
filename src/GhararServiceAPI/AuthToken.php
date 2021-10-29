<?php

namespace Gharar\MoodleModGharar\GhararServiceAPI;

class AuthToken
{
    public const PROP_TOKEN = "token";

    /** @var string */
    private $token;

    private function __construct(string $token)
    {
        $this->setToken($token);
    }

    public static function fromRawObject(object $object): self
    {
        $authToken = new self($object->{self::PROP_TOKEN});

        return $authToken;
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
