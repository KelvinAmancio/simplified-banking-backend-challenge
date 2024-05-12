<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Service\Auth;
use App\Service\Db\UserService;
use App\Service\Db\WalletService;
use App\Service\JwtWrapper;

class UserRegister
{
    public function __construct(
        private Auth $auth,
        private JwtWrapper $jwtWrapper,
        private UserService $userService,
        private WalletService $walletService
    ) {
    }

    public function execute(array $userData): array
    {
        $userAndToken = $this->saveUserAndBuildToken($userData);

        $walletData = [
            'owner_id' => $userAndToken['user']['uuid']
        ];

        $wallet = $this->walletService->save($walletData);

        return [
            ...$userAndToken,
            'wallet' => $wallet
        ];
    }

    private function saveUserAndBuildToken(array $userData): array
    {
        $userAttributes = [
            ...$userData,
            'password' => $this->auth->hashPassword($userData['password'])
        ];

        $user = $this->userService->save($userAttributes);

        $tokenData = [
            'user_uuid' => $user['uuid'],
            'user_type' => UserService::getType($userData['cpf_cnpj'])
        ];

        $token = $this->jwtWrapper->encode($tokenData);

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
