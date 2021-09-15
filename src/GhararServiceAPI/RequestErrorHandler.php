<?php

namespace MAChitgarha\MoodleModGharar\GhararServiceAPI;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use MAChitgarha\MoodleModGharar\GhararServiceAPI\Exception\{
    TimeoutException,
    UnhandledException,
    UnauthorizedException,
};

class RequestErrorHandler
{
    private const STATUS_CODE_OK = 200;
    private const STATUS_CODE_CREATED = 201;
    private const STATUS_CODE_ACCEPTED = 202;
    private const STATUS_CODE_UNAUTHORIZED = 401;

    /** @var ResponseInterface */
    private $response;

    public function __construct(callable $requestSender)
    {
        try {
            $this->response = $requestSender();

            if (self::isStatusCodeSuccessful(
                $this->response->getStatusCode())
            ) {
                return;
            }
            self::handleUnsuccessfulResponse($this->response);
        } catch (ConnectException $e) {
            self::handleTimeoutException($e);
        } catch (\Exception $e) {
            self::handleUnhandledException($e);
        }
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

        throw new UnhandledException(
            $response->getReasonPhrase(),
            $response->getStatusCode()
        );
        // "Request to Gharar API failed; reason: '" .
        // "', status code: " .
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
