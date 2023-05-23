<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\HasCurrentResource;
use MoonShine\Helpers\Condition;

class SwitchBoolean extends Checkbox implements HasCurrentResource
{
    protected static string $view = 'moonshine::fields.switch';

    protected bool $autoUpdate = true;

    public function autoUpdate(mixed $condition = null): static
    {
        $this->autoUpdate = Condition::boolean($condition, true);

        return $this;
    }

    public function readonly($condition = null): static
    {
        $this->autoUpdate(false);

        return parent::readonly($condition);
    }

    public function indexViewValue(Model $item, bool $container = true): string
    {
        if (! $this->autoUpdate || ! $container) {
            return parent::indexViewValue($item, $container);
        }

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->autoUpdate,
            'item' => $item,
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return (string)$item->{$this->field()};
    }
}
