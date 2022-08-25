<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Leeto\MoonShine\Traits\Models\HasMoonShineChangeLog;

class MoonshineUser extends Authenticatable
{
    use HasMoonShineChangeLog;
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'email',
        'moonshine_user_role_id',
        'password',
        'name',
        'avatar'
    ];

    protected $with = ['moonshineUserRole'];

    public function moonshineUserRole(): BelongsTo
    {
        return $this->belongsTo(MoonshineUserRole::class);
    }
}
