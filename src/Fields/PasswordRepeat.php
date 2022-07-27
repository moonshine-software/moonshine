<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class PasswordRepeat extends Field
{
    public static string $view = 'input';

    public static string $type = 'password';

    protected string $autocomplete = 'confirm-password';

    public function exportViewValue(Model $item): string
    {
        return '';
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        return '';
    }

    public function formViewValue(Model $item): string
    {
        return '';
    }

    public function save(Model $item): Model
    {
        return $item;
    }
}
