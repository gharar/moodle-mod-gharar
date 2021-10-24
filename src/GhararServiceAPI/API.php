<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use Webmozart\Json\JsonDecoder;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\ToBeCreatedRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Room\AvailableRoom;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AbstractMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableRoomMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\ToBeCreatedRoomMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\AvailableLiveMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Member\ToBeCreatedLiveMember;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception\{
    TimeoutException,
    UnauthorizedException,
    UnhandledException,
    DuplicatedRoomNameException,
};

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
    private const CONFIG_REQUEST_TIMEOUT = 5.0;

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
            RequestOptions::HTTP_ERRORS => true,
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
        try {
            $roomListRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->get(
                    RelativeURI::getRooms()
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        $roomList = [];
        foreach ($roomListRaw as $roomRaw) {
            $roomList[] = AvailableRoom::fromRawObject($roomRaw);
        }

        return $roomList;
    }

    public function createRoom(ToBeCreatedRoom $newRoom): AvailableRoom
    {
        try {
            $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->post(
                    RelativeURI::getRooms(),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedRoom::PROP_NAME => $newRoom->getName(),
                        ToBeCreatedRoom::PROP_IS_PRIVATE =>
                            $newRoom->isPrivate(),
                    ]]
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleDuplicatedRoomName()
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function retrieveRoom(string $roomAddress): AvailableRoom
    {
        try {
            $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->get(
                    RelativeURI::getRoom($roomAddress)
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function updateRoom(AvailableRoom $room): AvailableRoom
    {
        try {
            $roomRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->put(
                    RelativeURI::getRoom($room->getAddress()),
                    [RequestOptions::FORM_PARAMS => [
                        AvailableRoom::PROP_NAME => $room->getName(),
                        AvailableRoom::PROP_IS_PRIVATE => $room->isPrivate(),
                        AvailableRoom::PROP_IS_ACTIVE => $room->isActive(),
                    ]]
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleDuplicatedRoomName()
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableRoom::fromRawObject($roomRaw);
    }

    public function destroyRoom(string $roomAddress): void
    {
        try {
            $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->delete(
                    RelativeURI::getRoom($roomAddress)
                )
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
            $roomMemberListRaw =
                $this->getSuccessfulJsonResponseDecodedContents(
                    $this->client->get(
                        RelativeURI::getRoomMembers($roomAddress)
                    )
                );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        $roomMemberList = [];
        foreach ($roomMemberListRaw as $roomMemberRaw) {
            $roomMemberList[] = AvailableRoomMember::fromRawObject(
                $roomMemberRaw
            );
        }

        return $roomMemberList;
    }

    public function createRoomMember(
        string $roomAddress,
        ToBeCreatedRoomMember $newMember
    ): AvailableRoomMember {
        try {
            $memberRaw = $this->getSuccessfulJsonResponseDecodedContents(
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
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableRoomMember::fromRawObject($memberRaw);
    }

    public function updateRoomMember(
        string $roomAddress,
        AvailableRoomMember $member
    ): AvailableRoomMember {
        try {
            $memberRaw = $this->getSuccessfulJsonResponseDecodedContents(
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
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableRoomMember::fromRawObject($memberRaw);
    }

    public function destroyRoomMember(
        string $roomAddress,
        string $memberPhone
    ): void {
        try {
            $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->delete(
                    RelativeURI::getRoomMember(
                        $roomAddress,
                        $memberPhone
                    )
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
        $roomMembers = $this->listRoomMembers($roomAddress);

        foreach ($roomMembers as $member) {
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
            $liveMemberListRaw =
                $this->getSuccessfulJsonResponseDecodedContents(
                    $this->client->get(
                        RelativeURI::getLiveMembers($roomAddress)
                    )
                )->invitees;
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        $liveMemberList = [];
        foreach ($liveMemberListRaw as $liveMemberRaw) {
            $liveMemberList[] = AvailableLiveMember::fromRawObject(
                $liveMemberRaw
            );
        }

        return $liveMemberList;
    }

    public function createLiveMember(
        string $roomAddress,
        ToBeCreatedLiveMember $newMember
    ): AvailableLiveMember {
        try {
            $memberRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->post(
                    RelativeURI::getLiveMembers($roomAddress),
                    [RequestOptions::FORM_PARAMS => [
                        ToBeCreatedLiveMember::PROP_PHONE =>
                            $newMember->getPhone(),
                        ToBeCreatedLiveMember::PROP_NAME =>
                            $newMember->getName(),
                    ]]
                )
            )->users[0];
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AvailableLiveMember::fromRawObject($memberRaw);
    }

    public function hasLiveMember(
        string $roomAddress,
        string $memberPhone
    ): bool {
        $liveMembers = $this->listLiveMembers($roomAddress);

        foreach ($liveMembers as $member) {
            if ($member->getPhone() === $memberPhone) {
                return true;
            }
        }

        return false;
    }

    public function generateAuthToken(AbstractMember $member): AuthToken
    {
        try {
            $authTokenRaw = $this->getSuccessfulJsonResponseDecodedContents(
                $this->client->post(
                    RelativeURI::getAuthToken(),
                    [RequestOptions::FORM_PARAMS => [
                        AbstractMember::PROP_PHONE => $member->getPhone(),
                        AbstractMember::PROP_NAME => $member->getName(),
                    ]]
                )
            );
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        return AuthToken::fromRawObject($authTokenRaw);
    }

    public function listRoomRecordings(string $roomAddress): array
    {
        try {
            $roomRecordingListRaw =
                $this->getSuccessfulJsonResponseDecodedContents(
                    $this->client->get(
                        RelativeURI::getRoomRecordings($roomAddress)
                    )
                )->recordings;
        } catch (TransferException $e) {
            (new ErrorHandler($e))
                ->handleGeneralErrors()
                ->unhandled();
        }

        $roomRecordingList = [];
        foreach ($roomRecordingListRaw as $roomRecordingRaw) {
            $roomRecordingList[] = Recording::fromRawObject($roomRecordingRaw);
        }

        return $roomRecordingList;
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
        return $response
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
