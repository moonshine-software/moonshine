<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MoonShine\Models\MoonshineUser;

trait UserHasOneTrait
{
    public function user(): HasOne
    {
        return $this->hasOne(MoonshineUser::class, 'id', 'user_id');
    }
}
