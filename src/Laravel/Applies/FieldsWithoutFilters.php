<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Applies;

use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasManyThrough;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Relationships\HasOneThrough;
use MoonShine\Laravel\Fields\Relationships\MorphMany;
use MoonShine\Laravel\Fields\Relationships\MorphOne;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\HiddenIds;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Position;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\StackFields;

final class FieldsWithoutFilters
{
    public const LIST = [
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
        RelationRepeater::class,
    ];
}
