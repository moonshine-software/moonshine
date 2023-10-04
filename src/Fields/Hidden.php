<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\WithDefaultValue;

class Hidden extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.hidden';

    protected string $type = 'hidden';

    public static function actionCheckedIds(): self
    {
        return self::make('ids')->customAttributes([
            'class' => 'actionsCheckedIds',
        ]);
    }
}
