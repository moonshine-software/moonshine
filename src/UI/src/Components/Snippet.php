<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\View\ComponentSlot;
use MoonShine\Support\Enums\Color;

/**
 * @method static static make(string $value = '', string|Color $color = Color::INFO)
 */
final class Snippet extends MoonShineComponent
{
    protected string $view = 'moonshine::components.snippet';

    protected array $translates = [
        'copied' => 'moonshine::ui.copied',
    ];

    public function __construct(
        public string $value = '',
        public string|Color $color = Color::INFO,
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
