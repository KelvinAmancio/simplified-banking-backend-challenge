<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\Db\UserService;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AllowedToTransferMiddleware implements MiddlewareInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $payerAuth = $request->getAttribute('auth');

        if (!$payerAuth) {
            throw new UnauthorizedHttpException('Access token not provided or invalid');
        }

        $body = $request->getParsedBody();

        if (UserService::TYPE_PJ === $payerAuth['user_type']) {
            throw new UnauthorizedHttpException('User type not authorized to make this transfer');
        }

        if ($payerAuth['user_uuid'] == $body['payee']) {
            throw new UnauthorizedHttpException('Invalid transfer to himself');
        }

        return $handler->handle($request);
    }
}
