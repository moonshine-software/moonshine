<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\View\ComponentSlot;
use InvalidArgumentException;
use MoonShine\Support\Enums\Color;

/**
 * @method static static make(string $value = '', string|null|Color $color = Color::SECONDARY, ?string $label = null, ?int $limit = 150)
 */
final class PrettyLimit extends MoonShineComponent
{
    protected string $view = 'moonshine::components.pretty-limit';

    public function __construct(
        public string $value = '',
        public string|null|Color $color = Color::SECONDARY,
        public ?string $label = null,
        public ?int $limit = 150,
    ) {
        parent::__construct();

        if ($this->color === null) {
            $this->color = Color::SECONDARY;
        }

        if ($this->limit !== null && $this->limit < 100) {
            throw new InvalidArgumentException('Limit should be more than 100');
        }

        $this->color = $this->color instanceof Color ? $this->color->value : $this->color;
    }

    public function value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function color(string|Color $color): self
    {
        $this->color = $color instanceof Color ? $color->value : $color;

        return $this;
    }

    public function label(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function limit(?int $limit): self
    {
        if ($this->limit !== null && $this->limit < 100) {
            throw new InvalidArgumentException('Limit should be more than 100');
        }

        $this->limit = $limit;

        return $this;
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
