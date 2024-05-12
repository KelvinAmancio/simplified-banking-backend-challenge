<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Concerns\HasUlids;
use Hyperf\Database\Model\Relations\HasOne;

/**
 */
class User extends Model
{
    use HasUlids;

    public const TYPE_PF = 'PF';
    public const TYPE_PJ = 'PJ';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'uuid',
        'name',
        'email',
        'cpf_cnpj',
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    protected string $primaryKey = 'uuid';

    protected string $keyType = 'string';

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'owner_id', 'uuid');
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
