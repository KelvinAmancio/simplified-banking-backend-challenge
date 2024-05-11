<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Model\User;
use App\Model\Wallet;
use App\Service\Auth;
use App\Service\JwtWrapper;

class UserRegister
{
    public function __construct(private Auth $auth, private JwtWrapper $jwtWrapper)
    {
    }

    public function execute(array $userData): array
    {
        $user = $this->saveUser($userData);
        $userToken = $this->buildUserToken($user['uuid'], $userData['cpf_cnpj']);

        $wallet = $this->saveWallet($user['uuid']);

        return [
            'user' => $user,
            'token' => $userToken,
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

    private function buildUserToken(string $userUuid, string $cpfCnpj): string
    {
        $tokenData = [
            'user_uuid' => $userUuid,
            'user_type' => User::getType($cpfCnpj)
        ];

        return $this->jwtWrapper->encode($tokenData);
    }
}
