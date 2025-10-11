<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\View\ComponentSlot;
use MoonShine\Support\Enums\Color;

/** @method static static make(string $icon, ?int $size = null, string|Color $color = '', ?string $path = null) */
final class Icon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.icon';

    protected bool $custom = false;

    public function __construct(
        public string $icon,
        public ?int $size = null,
        public string|Color $color = '',
        public ?string $path = null,
    ) {
        parent::__construct();

        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;

        if (\in_array($this->path, [null, '', '0'], true)) {
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
            'slot' => new ComponentSlot($this->isCustom() ? $this->icon : ''),
        ];
    }
}
