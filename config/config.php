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
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

use function Hyperf\Support\env;

return [
    'app_name' => env('APP_NAME', 'simplified-banking-backend-challenge'),
    'app_env' => env('APP_ENV', 'dev'),
    'scan_cacheable' => env('SCAN_CACHEABLE', false),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
    // JWT
    'jwt_secret' => env('APP_SECRET', 'app_secret'),
    'jwt_token_expires' => 1800,
    // Payment Received Notifier
    'payment_received_notifier_base_uri' => env('PAYMENT_RECEIVED_NOTIFIER_BASE_URI', ''),
    // Transfer Authorizer
    'transfer_authorizer_base_uri' => env('TRANSFER_AUTHORIZER_BASE_URI', ''),
];
