<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Concerns\HasUlids;

/**
 */
class Wallet extends Model
{
    use HasUlids;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'uuid',
        'owner_id',
        'balance',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    protected array $attributes = [
        'balance' => 0
    ];

    protected string $primaryKey = 'uuid';

    protected string $keyType = 'string';
}
