<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Model\User;
use App\Model\Wallet;
use App\Service\Auth;

class UserRegister
{
    public function __construct(private Auth $auth)
    {
    }

    public function execute(array $userData): array
    {
        $user = $this->saveUser($userData);
        $wallet = $this->saveWallet($user['uuid']);

        return [
            'user' => $user,
            'wallet' => $wallet
        ];
    }

    private function saveUser(array $userData): array
    {
        $userAttributes = [
            ...$userData,
            'uuid' => User::buildUuid(),
            'password' => $this->auth->hashPassword($userData['password'])
        ];

        return User::create($userAttributes)->toArray();
    }

    private function saveWallet(string $userUuid): array
    {
        $walletData = [
            'uuid' => Wallet::buildUuid(),
            'owner_id' => $userUuid,
        ];

        return Wallet::create($walletData)->toArray();
    }
}
