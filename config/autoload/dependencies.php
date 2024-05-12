<?php

declare(strict_types=1);

use App\Service\JwtWrapper;
use App\Service\TransferReceivedNotifier;
use App\Service\TransferAuthorizer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
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
    TransferReceivedNotifier::class => function (ContainerInterface $c): TransferReceivedNotifier {
        return new TransferReceivedNotifier(
            (string) config('payment_received_notifier_base_uri'),
            $c->get(LoggerInterface::class)
        );
    },
    TransferAuthorizer::class => function (ContainerInterface $c): TransferAuthorizer {
        return new TransferAuthorizer(
            (string) config('transfer_authorizer_base_uri'),
            $c->get(LoggerInterface::class)
        );
    },
];
