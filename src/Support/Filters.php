<?php

declare(strict_types=1);

namespace MoonShine\Support;

use MoonShine\Fields\File;
use MoonShine\Fields\HiddenIds;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Position;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\HasManyThrough;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\HasOneThrough;
use MoonShine\Fields\Relationships\MorphMany;
use MoonShine\Fields\Relationships\MorphOne;
use MoonShine\Fields\StackFields;

final class Filters
{
    public const NO_FILTERS = [
        HiddenIds::class,
        Position::class,
        File::class,
        Image::class,
        HasOne::class,
        HasMany::class,
        HasManyThrough::class,
        HasOneThrough::class,
        MorphMany::class,
        MorphOne::class,
        Password::class,
        PasswordRepeat::class,
        StackFields::class,
        Preview::class,
    ];
}
