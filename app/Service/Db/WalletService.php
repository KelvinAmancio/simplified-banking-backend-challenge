<?php

declare(strict_types=1);

namespace App\Service\Db;

use App\Helper\DbHelper;
use App\Model\Wallet;

class WalletService
{
    use DbHelper;

    public function save(array $walletAttributes): array
    {
        $walletAttributes['uuid'] = $this->buildUuid();

        return Wallet::create($walletAttributes)->toArray();
    }

    public function updateBalance(string $userUuid, float $balanceValue): void
    {
        Wallet::query()->where('owner_id', $userUuid)->update(['balance' => $balanceValue]);
    }
}
