<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Modals\Modal;

trait WithModal
{
    protected ?Modal $modal = null;

    public function isInModal(): bool
    {
        return ! is_null($this->modal);
    }

    public function inModal(
        string $title = null,
        string $content = null,
        array $buttons = []
    ): self {
        $this->modal = Modal::make($title, $content)
            ->buttons($buttons);

        return $this;
    }

    public function withConfirm(
        string $title = null,
        string $content = null,
        ?array $buttons = null
    ): self {
        $this->modal = Modal::make(
            $title ?? __('moonshine::ui.confirm'),
            $content ?? __('moonshine::ui.confirm_message')
        )->buttons(
            $buttons ?? [
                ActionButton::make(
                    $title ?? __('moonshine::ui.confirm'),
                    '#'
                )
                    ->customAttributes(['class' => 'btn-pink'])
                    ->icon('heroicons.outline.trash')
                    ->showInLine(),
            ]
        );

        return $this;
    }

    public function modal(): ?Modal
    {
        return $this->modal;
    }
}
