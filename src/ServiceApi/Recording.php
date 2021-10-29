<?php

namespace Gharar\MoodleModGharar\ServiceApi;

class Recording
{
    public const PROP_URL = "url";
    public const PROP_NAME = "name";

    /** @var string */
    private $url;

    /** @var string */
    private $name;

    public function __construct(string $url)
    {
        $this->setUrl($url);
    }

    public static function fromRawObject(object $object): self
    {
        $recording = new self($url);
    }

    private function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
