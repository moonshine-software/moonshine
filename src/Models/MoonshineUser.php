<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Leeto\MoonShine\Traits\Models\HasMoonShineChangeLog;

class MoonshineUser extends Authenticatable
{
    use HasMoonShineChangeLog;
    use HasFactory;
    use Notifiable;

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


    public function moonshineSocialites(): HasMany
    {
        return $this->hasMany(MoonshineSocialite::class);
    }
}
