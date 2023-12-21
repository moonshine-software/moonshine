<?php

namespace MoonShine\UI;

use Closure;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Support\Condition;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;

/**
 * @method static static make(string|Closure|null $title, string|Closure|null $content, bool $async = false)
 */
class Modal
{
    use Makeable;
    use HasAsync;
    use WithComponentAttributes;

    protected array $buttons = [];

    protected bool $isWide = false;

    protected bool $isAuto = false;

    protected bool $isCloseOutside = false;

    protected bool $isAutoClose = true;

    public function __construct(
        protected string|Closure|null $title,
        protected string|Closure|null $content,
        bool $async = false,
    ) {
        if ($async) {
            $this->async('#');
        }
    }

    public function wide(mixed $condition = null): self
    {
        $this->isWide = Condition::boolean($condition, true);

        return $this;
    }

    public function autoClose(mixed $condition = null): self
    {
        $this->isAutoClose = Condition::boolean($condition, true);

        return $this;
    }

    public function isWide(): bool
    {
        return $this->isWide;
    }

    public function auto(mixed $condition = null): self
    {
        $this->isAuto = Condition::boolean($condition, true);

        return $this;
    }

    public function isAuto(): bool
    {
        return $this->isAuto;
    }

    public function isAutoClose(): bool
    {
        return $this->isAutoClose;
    }

    public function closeOutside(mixed $condition = null): self
    {
        $this->isCloseOutside = Condition::boolean($condition, true);

        return $this;
    }

    public function isCloseOutside(): bool
    {
        return $this->isCloseOutside;
    }

    public function title(mixed $data = null): ?string
    {
        return value($this->title, $data);
    }

    public function content(mixed $data = null): ?string
    {
        $content = value($this->content, $data);

        return is_null($content)
            ? null
            : (string) $content;
    }

    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(mixed $data = null): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->fillItem($data);
    }
}
