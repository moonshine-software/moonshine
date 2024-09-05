<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\View\ComponentSlot;
use MoonShine\Support\Enums\Color;

/**
 * @method static static make(string $value = '', string|Color $color = Color::PURPLE)
 */
final class Badge extends MoonShineComponent
{
    protected string $view = 'moonshine::components.badge';

    public function __construct(
        public string $value = '',
        public string|Color $color = Color::PURPLE
    ) {
        parent::__construct();

        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'slot' => new ComponentSlot($this->value),
        ];
    }
}
