<?php

declare(strict_types=1);

namespace App\Service\Db;

use App\Helper\DbHelper;
use App\Model\Transfer;

class TransferService
{
    use DbHelper;

    public function save(array $walletAttributes): array
    {
        $walletAttributes['uuid'] = $this->buildUuid();

        return Transfer::create($walletAttributes)->toArray();
    }
}
