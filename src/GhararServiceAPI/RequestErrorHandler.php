<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception\{
    TimeoutException,
    UnhandledException,
    UnauthorizedException,
    DuplicatedRoomNameException,
};

class RequestErrorHandler
{
    private const STATUS_CODE_OK = 200;
    private const STATUS_CODE_CREATED = 201;
    private const STATUS_CODE_ACCEPTED = 202;
    private const STATUS_CODE_BAD_REQUEST = 400;
    private const STATUS_CODE_UNAUTHORIZED = 401;

    /** @var ResponseInterface */
    private $response;

    public function __construct(callable $requestSender)
    {
        try {
            $this->response = $requestSender();
        } catch (ConnectException $e) {
            self::handleTimeoutException($e);
        } catch (\Exception $e) {
            self::handleUnhandledException($e);
        }

        if (!$this->response) {
            return;
        }

        if (self::isStatusCodeSuccessful(
            $this->response->getStatusCode())
        ) {
            return;
        }
        self::handleUnsuccessfulResponse($this->response);
    }

    private static function handleTimeoutException(
        ConnectException $exception
    ): void {
        if (preg_match("/(timeout|timed out)/i", $exception->getMessage())) {
            throw new TimeoutException();
        }
    }

    private static function handleUnhandledException(
        \Exception $exception
    ): void {
        throw new UnhandledException(
            $exception->getMessage()
        );
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
            throw new UnauthorizedException();
        }

        if ($statusCode === self::STATUS_CODE_BAD_REQUEST) {
            if (self::isErrorTypeOfRoomNameDuplicated($response)) {
                throw new DuplicatedRoomNameException();
            }
        }

        throw new UnhandledException(
            $response->getReasonPhrase(),
            $response->getStatusCode()
        );
    }

    private static function isErrorTypeOfRoomNameDuplicated(
        ResponseInterface $response
    ): bool {
        return preg_match(
            "/اتاق تکراری/ui",
            $response->getBody()->getContents()
        );
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
