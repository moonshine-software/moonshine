<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\Contracts\View\View;

trait WithIcon
{
    protected ?string $icon = null;

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconValue(): string
    {
        return $this->icon ?? '';
    }

    public function getIcon(
        int $size = 8,
        string $color = '',
        string $class = ''
    ): View|string {
        if ($this->iconValue() === '') {
            return '';
        }

        return view("moonshine::ui.icons.{$this->iconValue()}", array_merge([
            'size' => $size,
            'class' => $class,
        ], $color ? ['color' => $color] : []));
    }
}
