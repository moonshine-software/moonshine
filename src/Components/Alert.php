<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\Traits\WithSlotContent;

/** @method static static make(string $icon = 'heroicons.bell-alert', string $type = 'default', bool $removable = false) */
final class Alert extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.alert';

    public function __construct(
        public string $icon = 'heroicons.bell-alert',
        public string $type = 'default',
        public bool $removable = false,
    ) {
    }

    protected function viewData(): array
    {
        return [
            'slot' => $this->getSlot(),
        ];
    }
}
