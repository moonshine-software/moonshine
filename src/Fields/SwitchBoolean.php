<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\HasCurrentResource;
use MoonShine\Helpers\Condition;

class SwitchBoolean extends Checkbox implements HasCurrentResource
{
    protected static string $view = 'moonshine::fields.switch';

    protected bool $autoUpdate = true;

    public function readonly($condition = null): static
    {
        $this->autoUpdate(false);

        return parent::readonly($condition);
    }

    public function autoUpdate(mixed $condition = null): static
    {
        $this->autoUpdate = Condition::boolean($condition, true);

        return $this;
    }

    public function preview(): string
    {
        $container = true;

        if (! $this->autoUpdate || ! $container) {
            return parent::preview($container);
        }

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->autoUpdate,
        ])->render();
    }
}
