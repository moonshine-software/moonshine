<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\OffCanvasContract;
use MoonShine\Support\AlpineJs;
use Throwable;

/**
 * @method static static make(Closure|string $title = '', Closure|Renderable|string $content = '', Closure|string $toggler = '', Closure|string|null $asyncUrl = '', iterable $components = [])
 */
final class OffCanvas extends AbstractWithComponents implements OffCanvasContract
{
    protected string $view = 'moonshine::components.off-canvas';

    protected bool $left = false;

    protected bool $open = false;

    protected bool $wide = false;

    protected bool $full = false;

    protected bool $autoClose = true;

    /**
     * @var  array<string, mixed>
     *
     */
    protected array $togglerAttributes = [];

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     *
     * @throws Throwable
     */
    public function __construct(
        protected Closure|string $title = '',
        protected Closure|Renderable|string $content = '',
        protected Closure|string $toggler = '',
        protected Closure|string|null $asyncUrl = null,
        iterable $components = [],
        // anonymous component variables
        string $name = 'default'
    ) {
        parent::__construct($components);

        $this->name($name);
    }

    public function open(Closure|bool|null $condition = null): self
    {
        $this->open = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function left(Closure|bool|null $condition = null): self
    {
        $this->left = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function wide(Closure|bool|null $condition = null): self
    {
        $this->wide = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function full(Closure|bool|null $condition = null): self
    {
        $this->full = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function autoClose(Closure|bool|null $condition = null): self
    {
        $this->autoClose = \is_null($condition) || value($condition, $this);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     */
    public function togglerAttributes(array $attributes): self
    {
        $this->togglerAttributes = $attributes;

        return $this;
    }

    /**
     * @param string[] $events
     */
    public function toggleEvents(array $events, bool $onlyOpening = false, bool $onlyClosing = false): self
    {
        $data = [
            'data-opening-events' => AlpineJs::prepareEvents($events),
            'data-closing-events' => AlpineJs::prepareEvents($events),
        ];

        if ($onlyOpening) {
            unset($data['data-closing-events']);
        }

        if ($onlyClosing) {
            unset($data['data-opening-events']);
        }

        return $this->customAttributes($data);
    }

    public function alwaysLoad(): self
    {
        return $this->customAttributes(['data-always-load' => true]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'isLeft' => $this->left,
            'isWide' => $this->wide,
            'isFull' => $this->full,
            'isOpen' => $this->open,
            'isAutoClose' => $this->autoClose,
            'title' => value($this->title, $this),
            'async' => ! empty($this->asyncUrl),
            'asyncUrl' => value($this->asyncUrl, $this) ?? '',
            'toggler' => new ComponentSlot(value($this->toggler, $this), $this->togglerAttributes),
            'slot' => new ComponentSlot(value($this->content, $this)),
        ];
    }
}
