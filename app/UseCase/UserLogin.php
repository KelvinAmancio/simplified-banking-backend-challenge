<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Exception\BusinessException;
use App\Model\User;
use App\Service\Auth;
use App\Service\JwtWrapper;

class UserLogin
{
    public function __construct(private Auth $auth, private JwtWrapper $jwtWrapper)
    {
    }

    public function execute(array $userData): array
    {
        $user = User::where('email', $userData['email'])->firstOrFail()->toArray();

        $isValidPassword = $this->auth->verifyPassword($userData['password'], $user['password']);

        if (!$isValidPassword) {
            throw new BusinessException("Não foi possível fazer login");
        }

        $userToken = $this->buildUserToken($user['uuid'], $user['cpf_cnpj']);

        return ['token' => $userToken];
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
