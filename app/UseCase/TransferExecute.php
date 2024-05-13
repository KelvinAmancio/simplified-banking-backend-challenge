<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Event\TransferReceivedEvent;
use App\Exception\BusinessException;
use App\Service\Db\TransferService;
use App\Service\Db\UserService;
use App\Service\Db\WalletService;
use App\Service\TransferAuthorizer;
use Hyperf\DbConnection\Db;
use Psr\EventDispatcher\EventDispatcherInterface;

class TransferExecute
{
    public function __construct(
        private TransferAuthorizer $transferAuthorizer,
        private EventDispatcherInterface $eventDispatcher,
        private TransferService $transferService,
        private UserService $userService,
        private WalletService $walletService
    ) {
    }

    public function execute(array $transferData): array
    {
        $transfer = Db::transaction(fn() => $this->makeTransfer($transferData));

        $this->notifyPayee($transfer);

        return ['transfer' => $transfer];
    }

    public function makeTransfer(array $transferData): array
    {
        $transferValue = $transferData['value'];

        $usersIds = [
            $transferData['payer'],
            $transferData['payee'],
        ];

        [$payer, $payee] = $this->userService->getWithWallets($usersIds);

        $this->validatePayerBalance($payer, $transferValue);

        $this->walletService->updateBalance(
            $payer['uuid'],
            $payer['balance'] - $transferValue
        );

        $this->walletService->updateBalance(
            $payee['uuid'],
            $payee['balance'] + $transferValue
        );

        $this->authorizeTransfer($transferData);

        return $this->saveTransfer($payer['uuid'], $payee['uuid'], $transferValue);
    }

    public function notifyPayee(array $transferData): void
    {
        $this->eventDispatcher->dispatch(new TransferReceivedEvent($transferData));
    }

    private function validatePayerBalance(array $payer, float $transferValue): void
    {
        if ($payer['balance'] < $transferValue) {
            throw new BusinessException('Balance unavailable for this transfer');
        }
    }

    private function authorizeTransfer(array $transferData): array
    {
        $response = $this->transferAuthorizer->execute($transferData);

        return !empty($response['authorized'])
            ? $response
            : throw new BusinessException('Transfer not authorized');
    }

    private function saveTransfer(string $payerUuid, string $payeeUuid, float $transferValue): array
    {
        $transferAttributes = [
            'payer_id' => $payerUuid,
            'payee_id' => $payeeUuid,
            'value' => $transferValue,
            'authorized' => true
        ];

        return $this->transferService->save($transferAttributes);
    }
}
