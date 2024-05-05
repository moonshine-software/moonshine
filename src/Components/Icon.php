<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Illuminate\View\ComponentSlot;

/** @method static static make(string $icon, int $size = 5, string $color = '', ?string $path = null) */
final class Icon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.icon';

    protected bool $custom = false;

    public function __construct(
        public string $icon,
        public int $size = 5,
        public string $color = '',
        public ?string $path = null,
    ) {
        parent::__construct();

        if(empty($this->path)) {
            $this->path = 'moonshine::icons';
        }
    }

    public function isCustom(): bool
    {
        return $this->custom;
    }

    public function custom(): self
    {
        $this->custom = true;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'slot' => new ComponentSlot($this->isCustom() ? $this->icon : '')
        ];
    }
}
