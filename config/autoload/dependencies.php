<?php

declare(strict_types=1);

use App\Service\JwtWrapper;
use Psr\Container\ContainerInterface;
use function Hyperf\Config\config;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    JwtWrapper::class => function (ContainerInterface $c): JwtWrapper {
        return new JwtWrapper(
            (string) config('jwt_secret'),
            (int) config('jwt_token_expires')
        );
    },
];
