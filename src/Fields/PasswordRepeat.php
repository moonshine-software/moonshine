<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;

class PasswordRepeat extends Password
{
    public function save(Model $item): Model
    {
        return $item;
    }
}
