<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentSlot;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Support\Condition;

/**
 * @method static static make(Closure|string $title, Closure|View|string $content, Closure|View|ActionButton|string $outer = '', Closure|string|null $asyncUrl = '')
 */
final class Modal extends MoonShineComponent
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
        protected Closure|View|string $content = '',
        protected Closure|View|ActionButton|string $outer = '',
        protected Closure|string|null $asyncUrl = null,
    ) {
    }

    public function open(Closure|bool|null $condition = null): self
    {
        $this->open = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function closeOutside(Closure|bool|null $condition = null): self
    {
        $this->closeOutside = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function wide(Closure|bool|null $condition = null): self
    {
        $this->wide = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function auto(Closure|bool|null $condition = null): self
    {
        $this->auto = is_null($condition) || Condition::boolean($condition, false);

        return $this;
    }

    public function autoClose(Closure|bool|null $autoClose = null): self
    {
        $this->autoClose = is_null($autoClose) || Condition::boolean($autoClose, false);

        return $this;
    }

    public function outerAttributes(array $attributes): self
    {
        $this->outerAttributes = $attributes;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'isWide' => $this->wide,
            'isOpen' => $this->open,
            'isAuto' => $this->auto,
            'isAutoClose' => $this->autoClose,
            'isCloseOutside' => $this->closeOutside,
            'async' => ! empty($this->asyncUrl),
            'asyncUrl' => value($this->asyncUrl, $this) ?? '',
            'title' => value($this->title, $this),
            'slot' => new ComponentSlot(value($this->content, $this)),
            'outerHtml' => new ComponentSlot(value($this->outer, $this), $this->outerAttributes),
        ];
    }
}
