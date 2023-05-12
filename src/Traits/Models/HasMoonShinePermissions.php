<?php

declare(strict_types=1);

namespace MoonShine\Traits\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MoonShine\Models\MoonshineUserPermission;
use MoonShine\MoonShineAuth;

trait HasMoonShinePermissions
{
    public function moonshineUserPermission(): HasOne
    {
        return $this->hasOne(
            MoonshineUserPermission::class,
            'moonshine_user_id',
            MoonShineAuth::model()?->getKeyName() ?? 'id'
        );
    }
}
