<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Support\AlpineJs;
use Throwable;

/**
 * @method static static make(Closure|string $title, Closure|Renderable|string $content = '', Closure|Renderable|ActionButtonContract|string $outer = '', Closure|string|null $asyncUrl = '', iterable $components = [])
 */
final class Modal extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.modal';

    protected bool $open = false;

    protected bool $closeOutside = true;

    protected bool $wide = false;

    protected bool $auto = false;

    protected bool $autoClose = true;

    protected array $outerAttributes = [];

    public function __construct(
        protected Closure|string $title = '',
        protected Closure|Renderable|string $content = '',
        protected Closure|Renderable|ActionButtonContract|string $outer = '',
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

    public function closeOutside(Closure|bool|null $condition = null): self
    {
        $this->closeOutside = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function wide(Closure|bool|null $condition = null): self
    {
        $this->wide = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function auto(Closure|bool|null $condition = null): self
    {
        $this->auto = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function autoClose(Closure|bool|null $condition = null): self
    {
        $this->autoClose = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function outerAttributes(array $attributes): self
    {
        $this->outerAttributes = $attributes;

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
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $componentsHtml = $this->getComponents()->isNotEmpty()
            ? Components::make($this->getComponents())
            : '';

        $outer = value($this->outer, $this);

        if ($outer instanceof ActionButtonContract) {
            $outer->openModal();
        }

        return [
            'isWide' => $this->wide,
            'isOpen' => $this->open,
            'isAuto' => $this->auto,
            'isAutoClose' => $this->autoClose,
            'isCloseOutside' => $this->closeOutside,
            'async' => ! empty($this->asyncUrl),
            'asyncUrl' => value($this->asyncUrl, $this) ?? '',
            'title' => value($this->title, $this),
            'slot' => new ComponentSlot(value($this->content, $this) . $componentsHtml),
            'outerHtml' => new ComponentSlot($outer, $this->outerAttributes),
        ];
    }
}
