<?php

declare(strict_types=1);

namespace MoonShine\Traits\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Models\MoonshineSocialite;
use MoonShine\MoonShineAuth;

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
