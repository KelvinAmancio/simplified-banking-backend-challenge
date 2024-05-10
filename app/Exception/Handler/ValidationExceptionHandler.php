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
        $encodedBody = json_encode($body);

        if (! $response->hasHeader('content-type')) {
            $response = $response->addHeader('content-type', 'application/json; charset=utf-8');
        }

        return $response->setStatus($throwable->status)->setBody(new SwooleStream($encodedBody));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
