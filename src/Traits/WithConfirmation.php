<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Modals\ConfirmActionModal;

trait WithConfirmation
{
    protected bool $isConfirmed = false;

    protected ?ConfirmActionModal $modal = null;

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function withConfirm(
        string $title = null,
        string $content = null,
        string $confirmButtonText = null
    ): self {
        $this->isConfirmed = true;

        $this->modal = ConfirmActionModal::make($title, $content)
            ->confirmButtonText($confirmButtonText ?? $this->label());

        return $this;
    }

    public function modal(): ?ConfirmActionModal
    {
        return $this->modal;
    }
}
