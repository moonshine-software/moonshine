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
        if (! is_closure($title)) {
            $title = static fn (): Closure|string => $title;
        }

        return ActionButton::make($button, $url)
            ->inModal($title, $component);
    }

    public function inModal(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        array $buttons = [],
        bool $async = false
    ): static {
        $this->modal = Modal::make($title, $content, $async)
            ->buttons($buttons);

        return $this;
    }

    public function withConfirm(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $button = null,
    ): static {
        $this->modal = Modal::make(
            is_null($title) ? __('moonshine::ui.confirm') : $title,
            is_null($content) ? __('moonshine::ui.confirm_message') : $content
        )->buttons([
            ActionButton::make(
                is_null($button) ? __('moonshine::ui.confirm') : $button,
                $this->url()
            )->showInLine(),
        ]);

        return $this;
    }

    public function modal(): ?Modal
    {
        return $this->modal;
    }
}
