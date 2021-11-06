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

    public function __construct(string $url, string $name)
    {
        $this
            ->setUrl($url)
            ->setName($name);
    }

    public static function fromRawObject(object $object): self
    {
        $recording = new self(
            $object->{self::PROP_URL},
            $object->{self::PROP_NAME}
        );

        return $recording;
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
