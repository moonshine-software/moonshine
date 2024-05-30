<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Laravel\Models\MoonshineSocialite;
use MoonShine\Laravel\MoonShineAuth;

trait HasMoonShineSocialite
{
    public function moonshineSocialites(): HasMany
    {
        return $this->hasMany(
            MoonshineSocialite::class,
            'moonshine_user_id',
            MoonShineAuth::model()?->getKeyName() ?? 'id'
        );
    }
}
