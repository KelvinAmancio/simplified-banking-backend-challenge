<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpMessage\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();

        /** @var HttpException $httpException*/
        $httpException = $throwable;

        if (!$response->hasHeader('content-type')) {
            $response = $response
                ->withAddedHeader('content-type', 'application/json; charset=utf-8');
        }

        $error = [
            'code' => $httpException->getStatusCode(),
            'message' => $httpException->getMessage(),
        ];

        return $response
            ->withHeader('Server', 'Hyperf')
            ->withStatus($error['code'])
            ->withBody(new SwooleStream(json_encode($error, JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
