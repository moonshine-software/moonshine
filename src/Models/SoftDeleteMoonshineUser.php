<?php

declare(strict_types=1);

namespace MoonShine\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeleteMoonshineUser extends MoonshineUser
{
    use SoftDeletes;
}
