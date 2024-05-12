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
}
