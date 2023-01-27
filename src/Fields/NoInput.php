<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class NoInput extends Field
{
    public static string $view = 'moonshine::fields.no-input';

    public function save(Model $item): Model
    {
        return $item;
    }

}
