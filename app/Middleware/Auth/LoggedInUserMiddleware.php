<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Service\JwtWrapper;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggedInUserMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected ContainerInterface $container,
        private JwtWrapper $jwtWrapper
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $header = $request->getHeader('Authorization');
        $token = $header ? $this->getToken($header[0]) : '';
        $data = $this->jwtWrapper->decode($token);

        if (!$token || !$data) {
            throw new UnauthorizedHttpException('Access token not provided or invalid');
        }

        $request = $request->withAttribute('auth', [
            'user_uuid' => $data->user_uuid,
            'user_type' => $data->user_type,
        ]);

        return $handler->handle($request);
    }

    private function getToken(string $header): string
    {
        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
