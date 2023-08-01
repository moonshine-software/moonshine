<?php

declare(strict_types=1);

namespace MoonShine\ActionButtons;

use Closure;
use MoonShine\Actions\AbstractAction;
use MoonShine\Contracts\Actions\ActionButtonContract;

class ActionButton extends AbstractAction implements ActionButtonContract
{
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
