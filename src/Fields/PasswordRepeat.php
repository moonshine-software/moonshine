<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class PasswordRepeat extends Field
{
    public static string $view = 'moonshine::fields.input';

    public static string $type = 'password';

    protected array $attributes = ['autocomplete'];

    public function save(Model $item): Model
    {
        return $item;
    }
}
