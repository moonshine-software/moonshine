<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Helpers\Condition;

class SwitchBoolean extends Checkbox
{
    protected string $view = 'moonshine::fields.switch';

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

    protected function resolvePreview(): string
    {
        if ($this->isRawMode() && ! $this->autoUpdate) {
            return parent::resolvePreview();
        }

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->autoUpdate,
        ])->render();
    }
}
