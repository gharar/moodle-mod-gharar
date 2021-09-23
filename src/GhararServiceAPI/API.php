<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Webmozart\Json\JsonDecoder;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\Member;

/**
 * @todo Prevent error messages from being exposed, in each and every case. For
 * example, when access token is wrong.
 * @todo Create a builder class or function that automatically grabs the access
 * token config from admin settings and passes it to the constructor to create
 * an instance of the class. In this case, maybe, the class should remain
 * singleton.
 */
class API
{
    public const REGEX_ACCESS_TOKEN =
        "/^[\da-f]{40}\$/i";
    public const REGEX_ROOM_ADDRESS =
        "/^[\da-f]{8}(-[\da-f]{4}){3}-[\da-f]{12}\$/i";

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
            RequestOptions::HTTP_ERRORS => false,
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
     * @return AvailableRoom[]
     */
    public function listRooms(): array
    {
        $roomListRaw = $this->getSuccessfulJsonResponseDecodedContents(
            function () {
                return $this->client->get(
                    RelativeURI::getRooms()
                );
            }
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
            function () use ($newRoom) {
                return $this->client->post(
                    RelativeURI::getRooms(),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedRoom::PROP_NAME => $newRoom->getName(),
                        ToBeCreatedRoom::PROP_IS_PRIVATE =>
                            $newRoom->isPrivate(),
                    ]]
                );
            }
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function retrieveRoom(string $roomAddress): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($roomAddress) {
                return $this->client->get(
                    RelativeURI::getRoom($roomAddress)
                );
            }
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function updateRoom(AvailableRoom $room): AvailableRoom
    {
        $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($room) {
                return $this->client->put(
                    RelativeURI::getRoom($room->getAddress()),
                    [RequestOptions::FORM_PARAMS => [
                        AvailableRoom::PROP_NAME => $room->getName(),
                        AvailableRoom::PROP_IS_PRIVATE => $room->isPrivate(),
                        AvailableRoom::PROP_IS_ACTIVE => $room->isActive(),
                    ]]
                );
            }
        );

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function destroyRoom(string $roomAddress): void
    {
        $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($roomAddress) {
                return $this->client->delete(
                    RelativeURI::getRoom($roomAddress)
                );
            }
        );
    }

    public function createRoomMember(string $roomAddress, User $user): User
    {
        $userRaw = $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($roomAddress, $user) {
                return $this->client->post(
                    RelativeURI::getRoomMembers($roomAddress),
                    [RequestOptions::FORM_PARAMS => [
                        User::PROP_PHONE => $user->getPhone(),
                        User::PROP_IS_ADMIN => $user->isAdmin(),
                        User::PROP_NAME => $user->getName(),
                    ]]
                );
            }
        );

        return User::fromRawObject($userRaw);
    }

    public function destroyRoomMember(
        string $roomAddress,
        string $userPhone
    ): void {
        $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($roomAddress, $userPhone) {
                return $this->client->delete(
                    RelativeURI::getRoomMemeber(
                        $roomAddress,
                        $userPhone
                    )
                );
            }
        );
    }

    public function generateAuthToken(User $user): AuthToken {
        $authTokenRaw = $this->getSuccessfulJsonResponseDecodedContents(
            function () use ($user) {
                return $this->client->post(
                    RelativeURI::getAuthToken(),
                    [RequestOptions::FORM_PARAMS => [
                        User::PROP_PHONE => $user->getPhone(),
                        User::PROP_NAME => $user->getName(),
                    ]]
                );
            }
        );

        return AuthToken::fromRawObject($authTokenRaw);
    }

    /**
     * @return object|array
     */
    private function getSuccessfulJsonResponseDecodedContents(
        callable $requestSender
    ) {
        return $this->jsonDecoder->decode(
            self::getSuccessfulResponseContents($requestSender)
        );
    }

    private static function getSuccessfulResponseContents(
        callable $requestSender
    ): string {
        return (new RequestErrorHandler($requestSender))
            ->getResponse()
            ->getBody()
            ->getContents();
    }
}

// Inner classes of the class above (virtually)

class RelativeURI
{
    public static function getRooms(): string
    {
        return "rooms/";
    }

    public static function getRoom(string $roomAddress): string
    {
        return self::getRooms() . "$roomAddress/";
    }

    public static function getRoomMembers(string $roomAddress): string
    {
        return self::getSpecificRoom($roomAddress) . "users/";
    }

    public static function getRoomMemeber(
        string $roomAddress,
        string $memberPhone
    ): string {
        return self::getRoomMembers($roomAddress) . "$memberPhone/";
    }

    public static function getAuthToken(): string
    {
        return "auth/token/";
    }
}
