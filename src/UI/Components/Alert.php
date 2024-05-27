<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\UI\Traits\Components\WithSlotContent;

/** @method static static make(string $icon = 'bell-alert', string $type = 'default', bool $removable = false) */
final class Alert extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.alert';

    public function __construct(
        public string $icon = 'bell-alert',
        public string $type = 'default',
        public bool $removable = false,
    ) {
        parent::__construct();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => $this->getSlot(),
        ];
    }
}
