<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\UI\Modal;

trait WithModal
{
    protected ?Modal $modal = null;

    public function isInModal(): bool
    {
        return ! is_null($this->modal);
    }

    public function inModal(
        ?Closure $title = null,
        ?Closure $content = null,
        array $buttons = []
    ): self {
        $this->modal = Modal::make($title, $content)
            ->buttons($buttons);

        return $this;
    }

    public function withConfirm(): self
    {
        $this->modal = Modal::make(
            static fn (): array|string|null => __('moonshine::ui.confirm'),
            static fn (): array|string|null => __('moonshine::ui.confirm_message')
        )->buttons([
            ActionButton::make(
                __('moonshine::ui.confirm'),
                '#'
            )->showInLine(),
        ]);

        return $this;
    }

    public function modal(): ?Modal
    {
        return $this->modal;
    }
}
