<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use Webmozart\Json\JsonDecoder;
use Psr\Http\Message\ResponseInterface;

class API
{
    public const REGEX_ACCESS_TOKEN =
        "/^[\da-f]{40}\$/i";
    public const REGEX_ROOM_ADDRESS =
        "/^[\da-f]{8}(-[\da-f]{4}){3}-[\da-f]{12}\$/i";

    private const STATUS_CODE_OK = 200;
    private const STATUS_CODE_CREATED = 201;
    private const STATUS_CODE_ACCEPTED = 202;
    private const STATUS_CODE_UNAUTHORIZED = 401;

    private const BASE_URI = "https://gharar.ir/api/v1/service/";
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
            RequestOptions::TIMEOUT => self::REQUEST_TIMEOUT,
            RequestOptions::HEADERS => [
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

    private static function getRoomsRelativeUri(): string
    {
        return "rooms/";
    }

    private static function getSpecificRoomRelativeUri(
        string $roomAddress
    ): string {
        return self::getRoomsRelativeUri() . "$roomAddress/";
    }

    /**
     * @return Room[]
     */
    public function listRooms(): array
    {
        $roomListRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->get(
                self::getRoomsRelativeUri()
            )
        );

        $roomList = [];
        foreach ($roomListRaw as $roomRaw) {
            $roomList[] = AvailableRoom::fromRawObject($roomRaw);
        }

        return $roomList;
    }

    public function createRoom(ToBeCreatedRoom $newRoomInfo): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->post(
                self::getRoomsRelativeUri(),
                [RequestOptions::FORM_PARAMS => [
                    ToBeCreatedRoom::NAME => $newRoomInfo->getName(),
                    ToBeCreatedRoom::IS_PRIVATE => $newRoomInfo->isPrivate(),
                ]]
            )
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function retrieveRoom(string $roomAddress): Room
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->get(
                self::getSpecificRoomRelativeUri($roomAddress)
            )
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    /**
     * @return object|array
     */
    private function getSuccessfulJsonResponseDecodedContents(
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
        if (self::isStatusCodeSuccessful($response->getStatusCode())) {
            return;
        }
        self::handleUnsuccessfulResponse($response);
    }

    private static function isStatusCodeSuccessful(int $statusCode): bool
    {
        return
            $statusCode === self::STATUS_CODE_OK ||
            $statusCode === self::STATUS_CODE_CREATED ||
            $statusCode === self::STATUS_CODE_ACCEPTED;
    }

    private static function handleUnsuccessfulResponse(
        ResponseInterface $response
    ): void {
        $statusCode = $response->getStatusCode();

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
            $response->getReasonPhrase() . "', status code: " .
            $response->getStatusCode()
        );
    }
}
