<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Model\User;

class UserRegister
{
    public function __invoke(array $userData): array
    {
        $user = new User($userData);
        $user->setAttribute('uuid', $user->newUniqueId())->save();
        return $user->toArray();
    }
}
