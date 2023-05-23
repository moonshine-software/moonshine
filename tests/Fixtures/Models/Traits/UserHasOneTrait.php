<?php

namespace MoonShine\Tests\Fixtures\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MoonShine\Models\MoonshineUser;

trait UserHasOneTrait
{
    /**
     * Получение пользователя
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(MoonshineUser::class, 'id', 'user_id');
    }
}
