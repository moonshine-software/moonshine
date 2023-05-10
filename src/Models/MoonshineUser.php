<?php

declare(strict_types=1);

namespace MoonShine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use MoonShine\Traits\Models\HasMoonShineChangeLog;
use MoonShine\Traits\Models\HasMoonShinePermissions;

class MoonshineUser extends Authenticatable
{
    use HasMoonShineChangeLog;
    use HasMoonShinePermissions;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'email',
        'moonshine_user_role_id',
        'password',
        'name',
        'avatar',
    ];

    protected $with = ['moonshineUserRole'];

    public function moonshineUserRole(): BelongsTo
    {
        return $this->belongsTo(MoonshineUserRole::class);
    }


    public function moonshineSocialites(): HasMany
    {
        return $this->hasMany(MoonshineSocialite::class);
    }
}
