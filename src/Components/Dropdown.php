<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\View\ComponentSlot;

/**
 * @method static static make(Closure|string $title, Closure|string $toggler = '', Closure|View|string $content = '', Closure|array $items = [], string $placement = 'bottom-start')
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
        public string $placement = 'bottom-start',
    ) {
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

    protected function viewData(): array
    {
        return [
            'toggler' => new ComponentSlot(value($this->toggler, $this), $this->togglerAttributes),
            'slot' => new ComponentSlot(value($this->content, $this)),
            'footer' => new ComponentSlot(value($this->footer, $this)),
            'items' => value($this->items, $this),
        ];
    }
}
