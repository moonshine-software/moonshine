<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Helpers\Condition;
use Leeto\MoonShine\Traits\Fields\BooleanTrait;

class SwitchBoolean extends Field
{
    use BooleanTrait;

    protected static string $view = 'moonshine::fields.switch';

    protected bool $autoUpdate = true;

    public function autoUpdate(mixed $condition = null): static
    {
        $this->autoUpdate = Condition::boolean($condition, true);

        return $this;
    }

    public function indexViewValue(Model $item, bool $container = true): mixed
    {
        $this->disabled(!$this->autoUpdate);

        return view('moonshine::fields.switch', [
            'field' => $this,
            'autoUpdate' => $this->autoUpdate,
            'item' => $item
        ]);
    }

    public function exportViewValue(Model $item): mixed
    {
        return $item->{$this->field()};
    }
}
