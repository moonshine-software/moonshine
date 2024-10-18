<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use MoonShine\Support\Enums\Color;
use MoonShine\UI\Traits\Components\WithSlotContent;

/** @method static static make(string $icon = 'bell-alert', string|Color $type = 'default', bool $removable = false) */
final class Alert extends MoonShineComponent
{
    use WithSlotContent;

    protected string $view = 'moonshine::components.alert';

    public function __construct(
        public string $icon = 'bell-alert',
        public string|Color $type = 'default',
        public bool $removable = false,
    ) {
        $this->type = $this->type instanceof Color
            ? $this->type->value
            : $this->type;

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
