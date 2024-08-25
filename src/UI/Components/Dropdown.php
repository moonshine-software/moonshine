<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;

/**
 * @method static static make(?string $title = null, Closure|string $toggler = '', Closure|Renderable|string $content = '', Closure|array $items = [], bool $searchable = false, Closure|string $searchPlaceholder = '', string $placement = 'bottom-start')
 */
final class Dropdown extends MoonShineComponent
{
    protected string $view = 'moonshine::components.dropdown';

    protected array $togglerAttributes = [];

    public function __construct(
        public ?string $title = null,
        protected Closure|string $toggler = '',
        protected Closure|Renderable|string $content = '',
        protected Closure|array $items = [],
        protected bool $searchable = false,
        protected Closure|string $searchPlaceholder = '',
        public string $placement = 'bottom-start',
        public Closure|string $footer = '',
    ) {
        parent::__construct();
    }

    public function toggler(Closure|string $toggler): self
    {
        $this->toggler = $toggler;

        return $this;
    }

    /**
     * @param  Closure|string[]  $items
     *
     * @return $this
     */
    public function items(Closure|array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function content(Closure|Renderable|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function placement(string $placement): self
    {
        $this->placement = $placement;

        return $this;
    }

    public function searchable(Closure|bool|null $condition = null): static
    {
        $this->searchable = value($condition, $this) ?? true;

        return $this;
    }

    public function searchPlaceholder(Closure|string $placeholder): static
    {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    public function togglerAttributes(array $attributes): self
    {
        $this->togglerAttributes = $attributes;

        return $this;
    }

    public function footer(Closure|string $value): self
    {
        $this->footer = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'toggler' => new ComponentSlot(value($this->toggler, $this), $this->togglerAttributes),
            'slot' => new ComponentSlot(value($this->content, $this)),
            'footer' => new ComponentSlot(value($this->footer, $this)),
            'searchable' => $this->searchable,
            'searchPlaceholder' => value($this->searchPlaceholder, $this),
            'items' => value($this->items, $this),
        ];
    }
}
