<?php

namespace Gharar\MoodleModGharar\ServiceApi;

use GuzzleHttp\Exception\{
    ConnectException,
    RequestException,
    TransferException
};
use GuzzleHttp\{
    Client,
    RequestOptions
};
use Gharar\MoodleModGharar\ServiceApi\Exception\{
    DuplicatedRoomNameException,
    TimeoutException,
    UnauthorizedException,
    UnhandledException,
};
use Gharar\MoodleModGharar\ServiceApi\Member\{
    AbstractMember,
    AvailableLiveMember,
    AvailableRoomMember,
    ToBeCreatedLiveMember,
    ToBeCreatedRoomMember
};
use Gharar\MoodleModGharar\ServiceApi\Room\{
    AvailableRoom,
    ToBeCreatedRoom
};
use Gharar\MoodleModGharar\Util;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @todo Prevent error messages from being exposed, in each and every case. For
 * example, when access token is wrong.
 * @todo Create a builder class or function that automatically grabs the access
 * token config from admin settings and passes it to the constructor to create
 * an instance of the class. In this case, maybe, the class should remain
 * singleton.
 * @todo Add tests for this.
 */
class Api
{
    public const REGEX_ACCESS_TOKEN =
        "/^[\da-f]{40}\$/i";
    public const REGEX_ROOM_ADDRESS =
        "/^[\da-f]{8}(-[\da-f]{4}){3}-[\da-f]{12}\$/i";

    private const CONFIG_BASE_URI = "https://gharar.ir/api/v1/service/";
    private const CONFIG_REQUEST_TIMEOUT = 5.0;

    /** @var Client */
    private $client;

    /** @var Serializer */
    private $jsonSerializer;

    public function __construct(string $token)
    {
        $this
            ->initClient($token)
            ->initJsonSerializer();
    }

    private function initClient(string $token): self
    {
        $this->client = new Client([
            "base_uri" => self::CONFIG_BASE_URI,
            RequestOptions::TIMEOUT => self::CONFIG_REQUEST_TIMEOUT,
            RequestOptions::HEADERS => [
                "Authorization" => self::generateAuthorizationHeader($token),
            ],
            RequestOptions::HTTP_ERRORS => true,
        ]);

        return $this;
    }

    private static function generateAuthorizationHeader(string $token): string
    {
        return "Token $token";
    }

    private function initJsonSerializer(): self
    {
        $this->jsonSerializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncoder()]
        );

        return $this;
    }

    /**
     * @return AvailableRoom[]
     */
    public function listRooms(): array
    {
        try {
            return \array_map(
                function (array $room): AvailableRoom {
                    return $this->jsonSerializer->denormalize(
                        $room,
                        AvailableRoom::class
                    );
                },
                $this->decodeResponse(
                    $this->client->get(
                        RelativeURI::getRooms()
                    )
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function createRoom(ToBeCreatedRoom $newRoom): AvailableRoom
    {
        try {
            return $this->deserializeResponse(
                $this->client->post(
                    RelativeURI::getRooms(),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedRoom::PROP_NAME =>
                            $newRoom->getName(),
                        ToBeCreatedRoom::PROP_IS_PRIVATE =>
                            $newRoom->isPrivate(),
                    ]]
                ),
                AvailableRoom::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleDuplicatedRoomName()
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function retrieveRoom(string $roomAddress): AvailableRoom
    {
        try {
            return $this->deserializeResponse(
                $this->client->get(
                    RelativeURI::getRoom($roomAddress)
                ),
                AvailableRoom::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function updateRoom(AvailableRoom $room): AvailableRoom
    {
        try {
            return $this->deserializeResponse(
                $this->client->put(
                    RelativeURI::getRoom($room->getAddress()),
                    [RequestOptions::FORM_PARAMS => [
                        AvailableRoom::PROP_NAME => $room->getName(),
                        AvailableRoom::PROP_IS_PRIVATE => $room->isPrivate(),
                        AvailableRoom::PROP_IS_ACTIVE => $room->isActive(),
                    ]]
                ),
                AvailableRoom::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleDuplicatedRoomName()
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function destroyRoom(string $roomAddress): void
    {
        try {
            $this->client->delete(
                RelativeURI::getRoom($roomAddress)
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    /**
     * @return AvailableRoomMember[]
     */
    public function listRoomMembers(string $roomAddress): array
    {
        try {
            return \array_map(
                function (array $roomMember): AvailableRoomMember {
                    return $this->jsonSerializer->denormalize(
                        $roomMember,
                        AvailableRoomMember::class
                    );
                },
                $this->decodeResponse(
                    $this->client->get(
                        RelativeURI::getRoomMembers($roomAddress)
                    )
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function createRoomMember(
        string $roomAddress,
        ToBeCreatedRoomMember $newMember
    ): AvailableRoomMember {
        try {
            return $this->deserializeResponse(
                $this->client->post(
                    RelativeURI::getRoomMembers($roomAddress),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedRoomMember::PROP_PHONE =>
                            $newMember->getPhone(),
                        ToBeCreatedRoomMember::PROP_IS_ADMIN =>
                            $newMember->isAdmin(),
                        ToBeCreatedRoomMember::PROP_NAME =>
                            $newMember->getName(),
                    ]]
                ),
                AvailableRoomMember::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function updateRoomMember(
        string $roomAddress,
        AvailableRoomMember $member
    ): AvailableRoomMember {
        try {
            return $this->deserializeResponse(
                $this->client->put(
                    RelativeURI::getRoomMember(
                        $roomAddress,
                        $member->getPhone()
                    ),
                    [RequestOptions::FORM_PARAMS => [
                        AvailableRoomMember::PROP_PHONE =>
                            $member->getPhone(),
                        AvailableRoomMember::PROP_IS_ADMIN =>
                            $member->isAdmin(),
                        AvailableRoomMember::PROP_NAME =>
                            $member->getName(),
                    ]]
                ),
                AvailableRoomMember::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function destroyRoomMember(
        string $roomAddress,
        string $memberPhone
    ): void {
        try {
            $this->client->delete(
                RelativeURI::getRoomMember(
                    $roomAddress,
                    $memberPhone
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function hasRoomMember(
        string $roomAddress,
        string $memberPhone
    ): bool {
        foreach ($this->listRoomMembers($roomAddress) as $member) {
            if ($member->getPhone() === $memberPhone) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return AvailableLiveMember[]
     */
    public function listLiveMembers(string $roomAddress): array
    {
        try {
            return \array_map(
                function (array $liveMember): AvailableLiveMember {
                    return $this->jsonSerializer->denormalize(
                        $liveMember,
                        AvailableLiveMember::class
                    );
                },
                $this->decodeResponse(
                    $this->client->get(
                        RelativeURI::getLiveMembers($roomAddress)
                    )
                )["invitees"]
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function createLiveMember(
        string $roomAddress,
        ToBeCreatedLiveMember $newMember
    ): AvailableLiveMember {
        try {
            $liveMember = $this->decodeResponse(
                $this->client->post(
                    RelativeURI::getLiveMembers($roomAddress),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedLiveMember::PROP_PHONE =>
                            $newMember->getPhone(),
                        ToBeCreatedLiveMember::PROP_NAME =>
                            $newMember->getName(),
                    ]]
                )
            )["users"][0];

            return $this->jsonSerializer->denormalize(
                $liveMember,
                AvailableLiveMember::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function hasLiveMember(
        string $roomAddress,
        string $memberPhone
    ): bool {
        foreach ($this->listLiveMembers($roomAddress) as $member) {
            if ($member->getPhone() === $memberPhone) {
                return true;
            }
        }
        return false;
    }

    public function generateAuthToken(AbstractMember $member): AuthToken
    {
        try {
            return $this->deserializeResponse(
                $this->client->post(
                    RelativeURI::getAuthToken(),
                    [RequestOptions::FORM_PARAMS => [
                        AbstractMember::PROP_PHONE => $member->getPhone(),
                        AbstractMember::PROP_NAME => $member->getName(),
                    ]]
                ),
                AuthToken::class
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    public function listRoomRecordings(string $roomAddress): array
    {
        try {
            return \array_map(
                function (array $recording): Recording {
                    return $this->jsonSerializer->denormalize(
                        $recording,
                        Recording::class
                    );
                },
                $this->decodeResponse(
                    $this->client->get(
                        RelativeURI::getRoomRecordings($roomAddress)
                    )
                )["recordings"]
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }
    }

    private function deserializeResponse(
        ResponseInterface $response,
        string $resultingObjectType
    ): object {
        return $this->jsonSerializer->deserialize(
            $response->getBody()->getContents(),
            $resultingObjectType,
            JsonEncoder::FORMAT
        );
    }

    private function decodeResponse(ResponseInterface $response)
    {
        return $this->jsonSerializer->decode(
            $response->getBody()->getContents(),
            JsonEncoder::FORMAT
        );
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
        return self::getRoom($roomAddress) . "users/";
    }

    public static function getRoomMember(
        string $roomAddress,
        string $memberPhone
    ): string {
        return self::getRoomMembers($roomAddress) . "$memberPhone/";
    }

    public static function getAuthToken(): string
    {
        return "auth/token/";
    }

    public static function getLiveMembers(string $roomAddress): string
    {
        return self::getRoom($roomAddress) . "live-users/";
    }

    public static function getLiveMember(
        string $roomAddress,
        string $memberPhone
    ): string {
        return self::getLiveMembers($roomAddress) . "$memberPhone/";
    }

    public static function getRoomRecordings(string $roomAddress): string
    {
        return self::getRooms() . "$roomAddress/recordings/";
    }
}

class ErrorHandler
{
    private const STATUS_CODE_BAD_REQUEST = 400;
    private const STATUS_CODE_UNAUTHORIZED = 401;
    private const STATUS_CODE_FORBIDDEN = 403;
    private const STATUS_CODE_NOT_FOUND = 404;

    /** @var TransferException */
    private $exception;

    /** @var ResponseInterface|null */
    private $response;

    public function __construct(TransferException $exception)
    {
        $this->exception = $exception;
        $this->response = $exception instanceof RequestException ?
            $exception->getResponse() : null;
    }

    public function handleGeneralErrors(): self
    {
        if ($this->exception instanceof ConnectException) {
            $this->handleGeneralConnectionErrors($this->exception);
        }
        if ($this->exception instanceof RequestException) {
            $this->handleGeneralRequestErrors();
        }

        return $this;
    }

    private static function handleGeneralConnectionErrors(
        ConnectException $exception
    ): void {
        self::handleTimeout($exception);
    }

    private static function handleTimeout(ConnectException $exception): void
    {
        if (preg_match(
            "/(timeout|timed out)/i",
            $exception->getMessage()
        )) {
            throw new TimeoutException();
        }
    }

    private function handleGeneralRequestErrors(): void
    {
        self::handleUnauthorized();
    }

    private function handleUnauthorized(): void
    {
        if (
            $this->response !== null &&
            $this->response->getStatusCode() ===
                self::STATUS_CODE_UNAUTHORIZED
        ) {
            throw new UnauthorizedException();
        }
    }

    public function unhandled(): self
    {
        if ($this->response !== null) {
            throw new UnhandledException(
                $this->exception->getMessage(),
                $this->response->getStatusCode()
            );
        }

        throw new UnhandledException(
            $this->exception->getMessage(),
            $this->exception->getCode()
        );

        return $this;
    }

    public function handleDuplicatedRoomName(): self
    {
        if (
            $this->response !== null &&
            $this->response->getStatusCode() ===
                self::STATUS_CODE_BAD_REQUEST &&
            (bool)(preg_match(
                "/اتاق تکراری/ui",
                $this->response->getBody()->getContents()
            ))
        ) {
            throw new DuplicatedRoomNameException();
        }

        return $this;
    }
}
