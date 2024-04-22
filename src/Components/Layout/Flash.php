<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make(string $key = 'alert', string $type = 'info', bool $withToast = true, bool $removable = true)
 */
class Flash extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.flash';

    public function __construct(
        protected string $key = 'alert',
        protected string $type = 'info',
        protected bool $withToast = true,
        protected bool $removable = true,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type,
            'withToast' => $this->withToast,
            'removable' => $this->removable,
        ];
    }
}
