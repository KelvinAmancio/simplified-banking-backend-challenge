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

use Hyperf\Validation\ValidationException;
use Hyperf\Validation\ValidationExceptionHandler as BaseValidationExceptionHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Throwable;

class ValidationExceptionHandler extends BaseValidationExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();

        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors();

        if (!$response->hasHeader('content-type')) {
            $response = $response
                ->withAddedHeader('content-type', 'application/json; charset=utf-8');
        }

        $erros = json_encode([
            'code' => $throwable->status,
            'message' => 'Validation Error',
            'details' => $body->getMessages(),
        ], JSON_UNESCAPED_UNICODE);

        return $response->withStatus($throwable->status)->withBody(new SwooleStream($erros));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
