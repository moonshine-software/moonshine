<?php

declare(strict_types=1);

namespace MoonShine\Support;

use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasManyThrough;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\HasOneThrough;
use MoonShine\Fields\Relationships\MorphMany;
use MoonShine\Fields\Relationships\MorphOne;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Fields\Relationships\MorphToMany;
use MoonShine\Fields\StackFields;

final class Filters
{
    const NO_FILTERS = [
        HasOne::class,
        HasMany::class,
        HasManyThrough::class,
        HasOneThrough::class,
        MorphMany::class,
        MorphOne::class,
        MorphTo::class,
        MorphToMany::class,
        Password::class,
        PasswordRepeat::class,
        StackFields::class,
    ];
}