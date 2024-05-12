<?php

declare(strict_types=1);

namespace App\Service;

class TransferReceivedNotifier extends ExternalService
{
    private const NOTIFICATION_PATH = '/v3/54dc2cf1-3add-45b5-b5a9-6bf7e7f1f4a6';

    public function execute(array $payload): array
    {
        return $this->post(self::NOTIFICATION_PATH, $payload);
    }
}
