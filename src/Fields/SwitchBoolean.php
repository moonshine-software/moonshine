<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use MoonShine\Helpers\Condition;

class SwitchBoolean extends Checkbox
{
    protected string $view = 'moonshine::fields.switch';

    protected bool $autoUpdate = true;

    protected string $updateUrl = '';

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->autoUpdate(condition: false);

        return parent::readonly($condition);
    }

    public function autoUpdate(?Closure $url = null, mixed $condition = null): static
    {
        $this->updateUrl = is_null($url)
            ? ''
            : $url($this);

        $this->autoUpdate = Condition::boolean($condition, true);

        return $this;
    }

    public function isAutoUpdate(): bool
    {
        return $this->autoUpdate;
    }

    public function getUpdateUrl(): string
    {
        return $this->updateUrl;
    }

    protected function resolvePreview(): string
    {
        if (! $this->isAutoUpdate() && $this->isRawMode()) {
            return parent::resolvePreview();
        }

        return view('moonshine::fields.switch', [
            'element' => $this,
            'autoUpdate' => $this->isAutoUpdate(),
        ])->render();
    }
}
