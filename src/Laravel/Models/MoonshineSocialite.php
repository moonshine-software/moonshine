<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\Laravel\MoonShineAuth;

// todo move to package
class MoonshineSocialite extends Model
{
    protected $fillable = [
        'moonshine_user_id',
        'driver',
        'identity',
    ];

    public function moonshineUser(): BelongsTo
    {
        $model = MoonShineAuth::getModel();

        return $this->belongsTo(
            $model::class,
            'moonshine_user_id',
            $model?->getKeyName() ?? 'id',
        );
    }
}
