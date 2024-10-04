<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;
use MoonShine\Support\AlpineJs;

/**
 * @method static static make(Closure|string $title = '', Closure|Renderable|string $content = '', Closure|string $toggler = '', Closure|string|null $asyncUrl = '', iterable $components = [])
 */
final class OffCanvas extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.off-canvas';

    protected bool $left = false;

    protected bool $open = false;

    protected array $togglerAttributes = [];

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
        $this->open = is_null($condition) || value($condition, $this);

        return $this;
    }

    public function left(Closure|bool|null $condition = null): self
    {
        $this->left = is_null($condition) || value($condition, $this);

        return $this;
    }

    public function togglerAttributes(array $attributes): self
    {
        $this->togglerAttributes = $attributes;

        return $this;
    }

    /**
     * @param string[] $events
     */
    public function toggleEvents(array $events, bool $onlyOpening = false, $onlyClosing = false): self
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

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'isLeft' => $this->left,
            'isOpen' => $this->open,
            'title' => value($this->title, $this),
            'async' => ! empty($this->asyncUrl),
            'asyncUrl' => value($this->asyncUrl, $this) ?? '',
            'toggler' => new ComponentSlot(value($this->toggler, $this), $this->togglerAttributes),
            'slot' => new ComponentSlot(value($this->content, $this)),
        ];
    }
}
