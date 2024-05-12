<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Exception\BusinessException;
use App\Service\Auth;
use App\Service\Db\UserService;
use App\Service\JwtWrapper;

class UserLogin
{
    public function __construct(
        private Auth $auth,
        private JwtWrapper $jwtWrapper,
        private UserService $userService
    ) {
    }

    public function execute(array $userData): array
    {
        $user = $this->userService->findByEmailOrFail($userData['email']);

        $isValidPassword = $this->auth->verifyPassword($userData['password'], $user['password']);

        if (!$isValidPassword) {
            throw new BusinessException('Não foi possível fazer login');
        }

        $userToken = $this->buildUserToken($user['uuid'], $user['cpf_cnpj']);

        return ['token' => $userToken];
    }

    private function buildUserToken(string $userUuid, string $cpfCnpj): string
    {
        $tokenData = [
            'user_uuid' => $userUuid,
            'user_type' => UserService::getType($cpfCnpj)
        ];

        return $this->jwtWrapper->encode($tokenData);
    }
}
