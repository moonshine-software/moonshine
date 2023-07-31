<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use MoonShine\Contracts\Actions\ActionButtonContract;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\InDropdownOrLine;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithHtmlAttributes;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithModal;

final class ActionButton implements ActionButtonContract
{
    use Makeable;
    use WithIcon;
    use WithHtmlAttributes;
    use WithLabel;
    use HasCanSee;
    use InDropdownOrLine;
    use WithModal;

    protected bool $isBulk = false;

    public function __construct(
        string $label,
        protected Closure|string|null $url = null,
        protected mixed $item = null
    ) {
        $this->setLabel($label);
    }

    public function bulk(): self
    {
        $this->isBulk = true;

        return $this;
    }

    public function isBulk(): bool
    {
        return $this->isBulk;
    }

    public function setItem(mixed $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function url(): string
    {
        return is_callable($this->url)
            ? call_user_func($this->url, $this->item)
            : $this->url;
    }

}
