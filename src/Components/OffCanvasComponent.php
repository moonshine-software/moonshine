<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\Condition;

/**
 * @method static static make(Closure|string $title, Closure|View|string $content, Closure|string $toggler = '', Closure|string|null $asyncUrl = '')
 */
final class OffCanvasComponent extends MoonShineComponent
{
    protected string $view = 'moonshine::components.offcanvas';

    protected bool $left = false;

    protected bool $open = false;

    protected array $togglerAttributes = [];

    public function __construct(
        protected Closure|string $title = '',
        protected Closure|View|string $content = '',
        protected Closure|string $toggler = '',
        protected Closure|string|null $asyncUrl = null
    ) {
    }

    public function open(Closure|bool|null $condition = null): self
    {
        $this->open = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function left(Closure|bool|null $condition = null): self
    {
        $this->left = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function togglerAttributes(array $attributes): self
    {
        $this->togglerAttributes = $attributes;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'left' => $this->left,
            'open' => $this->open,
            'title' => value($this->title, $this),
            'async' => ! is_null($this->asyncUrl),
            'asyncUrl' => value($this->asyncUrl, $this) ?? '',
            'toggler' => new ComponentSlot(value($this->toggler, $this), $this->togglerAttributes),
            'slot' => new ComponentSlot(value($this->content, $this)),
        ];
    }
}
