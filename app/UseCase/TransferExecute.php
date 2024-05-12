<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Event\TransferReceivedEvent;
use App\Model\Transfer;
use App\Model\User;
use App\Model\Wallet;
use App\Service\TransferAuthorizer;
use Hyperf\DbConnection\Db;
use Psr\EventDispatcher\EventDispatcherInterface;

class TransferExecute
{
    public function __construct(
        private TransferAuthorizer $transferAuthorizer,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function execute(array $transferData): array
    {
        $transfer = Db::transaction(fn() => $this->makeTransfer($transferData));

        $this->notifyPayee($transfer);

        return ['transfer' => $transfer];
    }

    private function makeTransfer(array $transferData): array
    {
        $transferValue = $transferData['value'];

        [$payer, $payee] = $this->getUsersWallets($transferData);

        $this->validatePayerRequirements($payer, $transferValue);

        $this->updateBalance($payer['uuid'], $payer['balance'] - $transferValue);

        $this->updateBalance($payee['uuid'], $payee['balance'] + $transferValue);

        $this->authorizeTransfer($transferData);

        return $this->saveTransfer($payer['uuid'], $payee['uuid'], $transferValue);
    }

    private function getUsersWallets(array $transferData): array
    {
        $usersIds = [
            $transferData['payer'],
            $transferData['payee']
        ];

        [$payer, $payee] = User
            ::query()
            ->whereIn('uuid', $usersIds)
            ->with('wallet')
            ->get()
            ->toArray();

        return [
            [
                'uuid' => $payer['uuid'],
                'cpf_cnpj' => $payer['cpf_cnpj'],
                'balance' => $payer['wallet']['balance'],
            ],
            [
                'uuid' => $payee['uuid'],
                'cpf_cnpj' => $payee['cpf_cnpj'],
                'balance' => $payee['wallet']['balance'],
            ],
        ];
    }

    private function validatePayerRequirements(array $payer, float $transferValue): void
    {
        if ($payer['balance'] < $transferValue) {
            throw new \Exception("Balance unavailable for this transfer");
        }

        if (User::isTypePJ($payer['cpf_cnpj'])) {
            throw new \Exception("User not authorized to make this transfer");
        }
    }

    private function updateBalance(string $userUuid, float $balanceValue): void
    {
        Wallet::query()->where('owner_id', $userUuid)->update(['balance' => $balanceValue]);
    }

    private function authorizeTransfer(array $transferData): array
    {
        $response = $this->transferAuthorizer->execute($transferData);

        return !empty($response['authorized'])
            ? $response
            : throw new \Exception("Transfer not authorized");
    }

    private function notifyPayee(array $transferData): void
    {
        $this->eventDispatcher->dispatch(new TransferReceivedEvent($transferData));
    }

    private function saveTransfer(string $payerUuid, string $payeeUuid, float $transferValue): array
    {
        $transferAttributes = [
            'uuid' => Transfer::buildUuid(),
            'payer_id' => $payerUuid,
            'payee_id' => $payeeUuid,
            'value' => $transferValue,
            'authorized' => true
        ];

        return Transfer::create($transferAttributes)->toArray();
    }
}
