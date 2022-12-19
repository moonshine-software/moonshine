<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

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
