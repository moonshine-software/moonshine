<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MoonShine\Models\MoonshineUser;

trait UserBelongsToTrait
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class, 'moonshine_user_id');
    }
}
