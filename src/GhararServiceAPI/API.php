<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use GuzzleHttp\Client;
use Webmozart\Json\JsonDecoder;
use Psr\Http\Message\ResponseInterface;

class API
{
    /** @var int */
    private const STATUS_CODE_SUCCESS = 200;
    /** @var int */
    private const STATUS_CODE_UNAUTHORIZED = 401;

    /** @var string */
    private const BASE_URI = "https://gharar.ir/api/v1/service/";
    /** @var double */
    private const REQUEST_TIMEOUT = 4.0;

    /** @var Client */
    private $client;

    /** @var JsonDecoder */
    private $jsonDecoder;

    public function __construct(string $token)
    {
        $this
            ->initClient($token)
            ->initJsonDecoder();
    }

    private function initClient(string $token): self
    {
        $this->client = new Client([
            "base_uri" => self::BASE_URI,
            "timeout" => self::REQUEST_TIMEOUT,
            "headers" => [
                "Authorization" => self::generateAuthorizationHeader($token),
            ],
        ]);
        return $this;
    }

    private static function generateAuthorizationHeader(string $token): string
    {
        return "Token $token";
    }

    private function initJsonDecoder(): self
    {
        $this->jsonDecoder = new JsonDecoder();
        return $this;
    }

    /**
     * @return Room[]
     */
    public function listRooms(): array
    {
        $roomListRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->get("rooms")
        );

        $roomList = [];
        foreach ($roomListRaw as $roomRaw) {
            $roomList[] = Room::fromRawObject($roomRaw);
        }

        return $roomList;
    }

    /**
     * @return object|array
     */
    private static function getSuccessfulJsonResponseDecodedContents(
        ResponseInterface $response
    ) {
        return $this->jsonDecoder->decode(
            self::getSuccessfulResponseContents($response)
        );
    }

    private static function getSuccessfulResponseContents(
        ResponseInterface $response
    ): string {
        self::assertSuccessfulResponse($response);
        return $response->getBody()->getContents();
    }

    private static function assertSuccessfulResponse(
        ResponseInterface $response
    ): void {
        $statusCode = $response->getStatusCode();

        if ($statusCode === self::STATUS_CODE_SUCCESS) {
            return;
        }
        if ($statusCode === self::STATUS_CODE_UNAUTHORIZED) {
            // TODO: Convert it to a custom exception
            throw new \InvalidArgumentException(
                // TODO: Use Util::getString() and use the reason phrase
                "Bad authorization token given"
            );
        }

        // TODO: Improve and specialize it
        throw new \Exception(
            "Request to Gharar API failed; reason: '" .
            $response->getReasonPhrase() . "'"
        );
    }
}
