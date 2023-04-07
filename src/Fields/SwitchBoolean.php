<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Contracts\View\View;
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

    public function indexViewValue(Model $item, bool $container = true): string
    {
        $this->disabled(! $this->autoUpdate);

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->autoUpdate,
            'item' => $item,
        ])->render();
    }

    public function exportViewValue(Model $item): string
    {
        return (string) $item->{$this->field()};
    }

    public function readonly($condition = null): static
    {
        $this->autoUpdate(false);

        return parent::readonly($condition);
    }
}
