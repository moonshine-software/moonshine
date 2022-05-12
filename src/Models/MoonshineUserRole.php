<?php

namespace Leeto\MoonShine\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoonshineUserRole extends Model
{
    public static int $MOONSHINE_DEFAULT_ROLE_ID = 1;

    protected $fillable = ['name'];

    public function moonshineUsers(): HasMany
    {
        return $this->hasMany(MoonshineUser::class);
    }
}
