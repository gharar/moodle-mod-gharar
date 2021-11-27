<?php

namespace Gharar\MoodleModGharar\ServiceApi;

class Recording
{
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
