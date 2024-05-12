<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Concerns\HasUlids;

/**
 */
class Transfer extends Model
{
    use HasUlids;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'transfers';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'uuid',
        'payer_id',
        'payee_id',
        'value',
        'authorized',
        'notification_sent',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [];

    protected array $attributes = [
        'authorized' => false,
        'notification_sent' => false,
    ];

    protected string $primaryKey = 'uuid';

    protected string $keyType = 'string';
}
