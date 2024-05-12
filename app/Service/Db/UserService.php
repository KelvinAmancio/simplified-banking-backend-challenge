<?php

declare(strict_types=1);

namespace App\Service\Db;

use App\Helper\DbHelper;
use App\Model\User;

class UserService
{
    use DbHelper;

    public const TYPE_PF = 'PF';
    public const TYPE_PJ = 'PJ';

    public function findByEmailOrFail(string $email): array
    {
        return User::where('email', $email)->firstOrFail()->toArray();
    }

    public function save(array $userAttributes): array
    {
        $userAttributes['uuid'] = $this->buildUuid();

        return User::create($userAttributes)->toArray();
    }

    public static function getType(string $cpfCnpj): string
    {
        return strlen($cpfCnpj) == 14 ? self::TYPE_PF : self::TYPE_PJ;
    }

    public static function isTypePJ(string $cpfCnpj): bool
    {
        return strlen($cpfCnpj) > 14;
    }
}
