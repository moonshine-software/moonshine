<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\View\ComponentSlot;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Traits\WithIcon;

/**
 * @method static static make(string $value = '', string|Color $color = Color::PURPLE, ?string $icon = null)
 */
final class Badge extends MoonShineComponent
{
    use WithIcon;

    protected string $view = 'moonshine::components.badge';

    public function __construct(
        public string $value = '',
        public string|Color $color = Color::PURPLE,
        ?string $icon = null,
    ) {
        parent::__construct();

        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;

        if ($icon !== null) {
            $this->icon($icon);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => new ComponentSlot($this->value),
            'icon' => new ComponentSlot($this->getIcon()),
        ];
    }
}
