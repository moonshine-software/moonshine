<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Helpers\Condition;

class SwitchBoolean extends Checkbox implements HasResourceContract
{
    protected static string $view = 'moonshine::fields.switch';

    protected bool $autoUpdate = true;

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->autoUpdate(false);

        return parent::readonly($condition);
    }

    public function autoUpdate(mixed $condition = null): static
    {
        $this->autoUpdate = Condition::boolean($condition, true);

        return $this;
    }

    public function resolvePreview(): string
    {
        if (! $this->autoUpdate) {
            return parent::resolvePreview();
        }

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->autoUpdate,
        ])->render();
    }
}
