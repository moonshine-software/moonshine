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

    protected bool $valueRaw = false;

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

    public function value(string $value): self
    {
        $this->value = $value;
        $this->valueRaw = false;

        return $this;
    }

    public function valueHtml(string $value): self
    {
        $this->value = $value;
        $this->valueRaw = true;

        return $this;
    }

    public function isValueRaw(): bool
    {
        return $this->valueRaw;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'value' => $this->value,
            'valueRaw' => $this->isValueRaw(),
            'icon' => new ComponentSlot($this->getIcon()),
        ];
    }
}
