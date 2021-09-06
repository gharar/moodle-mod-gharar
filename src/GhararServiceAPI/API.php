<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Webmozart\Json\JsonDecoder;
use Psr\Http\Message\ResponseInterface;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\User;

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

    private const CONFIG_BASE_URI = "https://gharar.ir/api/v1/service/";
    private const CONFIG_REQUEST_TIMEOUT = 4.0;

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
            "base_uri" => self::CONFIG_BASE_URI,
            RequestOptions::TIMEOUT => self::CONFIG_REQUEST_TIMEOUT,
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

    private static function getRoomUsersRelativeUri(string $roomAddress): string
    {
        return self::getSpecificRoomRelativeUri($roomAddress) . "users/";
    }

    private static function getSpecificRoomUserRelativeUri(
        string $roomAddress,
        string $phone
    ): string {
        return self::getRoomUsersRelativeUri($roomAddress) . "$phone/";
    }

    /**
     * @return AvailableRoom[]
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

    public function createRoom(ToBeCreatedRoom $newRoom): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->post(
                self::getRoomsRelativeUri(),
                [RequestOptions::FORM_PARAMS => [
                    ToBeCreatedRoom::PROP_NAME => $newRoom->getName(),
                    ToBeCreatedRoom::PROP_IS_PRIVATE => $newRoom->isPrivate(),
                ]]
            )
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function retrieveRoom(string $roomAddress): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->get(
                self::getSpecificRoomRelativeUri($roomAddress)
            )
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function updateRoom(AvailableRoom $room): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->put(
                self::getSpecificRoomRelativeUri($room->getAddress()),
                [RequestOptions::FORM_PARAMS => [
                    AvailableRoom::PROP_NAME => $room->getName(),
                    AvailableRoom::PROP_IS_PRIVATE => $room->isPrivate(),
                    AvailableRoom::PROP_IS_ACTIVE => $room->isActive(),
                ]]
            )
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function destroyRoom(string $roomAddress): void
    {
        $this->client->delete(
            self::getSpecificRoomRelativeUri($roomAddress)
        );
    }

    public function createRoomMember(string $roomAddress, User $user): User
    {
        $userRaw = $this->getSuccessfulJsonResponseDecodedContents(
            $this->client->post(
                self::getRoomUsersRelativeUri($roomAddress),
                [RequestOptions::FORM_PARAMS => [
                    User::PROP_PHONE => $user->getPhone(),
                    User::PROP_IS_ADMIN => $user->isAdmin(),
                    User::PROP_NAME => $user->getName(),
                ]]
            )
        );

        return User::fromRawObject($userRaw);
    }

    public function destroyRoomMember(
        string $roomAddress,
        string $userPhone
    ): void {
        $this->client->delete(
            self::getSpecificRoomUserRelativeUri($roomAddress, $userPhone)
        );
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
