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
        $slot = new ComponentSlot($this->isCustom() ? $this->icon : '');

        return [
            'slot' => $slot,
            'hasSlot' => $slot->isNotEmpty(),
            'iconView' => $this->icon !== '' ? "$this->path.$this->icon" : null,
        ];
    }

    protected function prepareViewData(array $data): array
    {
        return [
            'wrapperAttributes' => $this->getAttributes()
                ->except(['path', 'icon', 'size', 'color'])
                ->class(array_filter([
                    'icon-wrapper',
                    $this->size ? "size-$this->size" : null,
                    empty($this->color) ? 'text-current' : "text-$this->color",
                ])),
        ];
    }
}
