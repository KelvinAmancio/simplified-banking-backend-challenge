<?php

declare(strict_types=1);

namespace App\Service;


class TransferAuthorizer extends ExternalService
{
    private const TRANSFER_AUTHORIZATION_PATH = '/v3/5794d450-d2e2-4412-8131-73d0293ac1cc';

    public function execute(array $payload): array
    {
        return $this->post(self::TRANSFER_AUTHORIZATION_PATH, $payload);
    }
}
