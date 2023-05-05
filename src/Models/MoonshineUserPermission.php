<?php

declare(strict_types=1);

namespace MoonShine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\MoonShineAuth;

class MoonshineUserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'moonshine_user_id',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'collection',
    ];

    public function moonshineUser(): BelongsTo
    {
        $model = MoonShineAuth::model();

        return $this->belongsTo(
            $model::class,
            'moonshine_user_id',
            $model->getKeyName(),
        );
    }
}
