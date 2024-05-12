<?php

declare(strict_types=1);

namespace App\UseCase;

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
        try {
            return $this->getToken($userData['email'], $userData['password']);
        } catch (\Throwable $th) {
            throw new \Exception("N'ao foi poss[ivel fazer login");
        }
    }

    private function getToken(string $email, string $password): array
    {
        $user = User::where('email', $email)->firstOrFail()->toArray();

        $isValidPassword = $this->auth->verifyPassword($password, $user['password']);

        if (!$isValidPassword) {
            throw new \Exception("N'ao foi poss[ivel fazer login");
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
