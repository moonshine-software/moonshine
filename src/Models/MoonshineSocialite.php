<?php

declare(strict_types=1);

namespace MoonShine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoonshineSocialite extends Model
{
    protected $fillable = [
        'moonshine_user_id',
        'driver',
        'identity',
    ];

    public function moonshineUser(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class);
    }
}
