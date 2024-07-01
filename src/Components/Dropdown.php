<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\Condition;
use function Symfony\Component\Translation\t;

/**
 * @method static static make(?string $title = null, Closure|string $toggler = '', Closure|View|string $content = '', Closure|bool $isSearchable = false, Closure|array $items = [], string $placement = 'bottom-start')
 */
final class Dropdown extends MoonShineComponent
{
    protected string $view = 'moonshine::components.dropdown';

    protected array $togglerAttributes = [];

    protected Closure|string $footer = '';

    public function __construct(
        public ?string $title = null,
        protected Closure|string $toggler = '',
        protected Closure|View|string $content = '',
        protected Closure|array $items = [],
        protected Closure|bool $isSearchable = false,
        protected Closure|string $searchPlaceholder = '',
        public string $placement = 'bottom-start',
    ) {
    }

    public function toggler(Closure|string $toggler): self
    {
        $this->toggler = $toggler;

        return $this;
    }

    public function items(Closure|array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function content(Closure|View|string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function searchable(Closure|bool|null $condition = null): static
    {
        $this->isSearchable = Condition::boolean($condition, true);

        return $this;
    }

    public function setSearchPlaceholder(Closure|string $placeholder): static
    {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }


    public function placement(string $placement): self
    {
        $this->placement = $placement;

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
            'searchable' => $this->isSearchable,
            'searchPlaceholder' => $this->searchPlaceholder,
            'items' => value($this->items, $this),
        ];
    }
}
