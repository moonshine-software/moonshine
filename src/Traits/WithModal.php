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

    public static function makeModal(
        Closure|string $button,
        Closure|string $title,
        string $url,
        ?Closure $component
    ): ActionButton {
        if(!is_closure($title)) {
            $title = fn() => $title;
        }

        return ActionButton::make($button, $url)
            ->inModal($title, $component);
    }

    public function inModal(
        ?Closure $title = null,
        ?Closure $content = null,
        array $buttons = [],
        bool $async = false
    ): static {
        $this->modal = Modal::make($title, $content, $async)
            ->buttons($buttons);

        return $this;
    }

    public function withConfirm(): static
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
