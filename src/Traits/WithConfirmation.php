<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Modals\ConfirmActionModal;

trait WithConfirmation
{
    protected bool $confirmation = false;

    protected ?ConfirmActionModal $modal = null;

    public function confirmation(): bool
    {
        return $this->confirmation;
    }

    public function withConfirm(
        string $title = null,
        string $content = null,
        string $confirmButtonText = null
    ): self {
        $this->confirmation = true;

        $this->modal = ConfirmActionModal::make($title, $content)
            ->confirmButtonText($confirmButtonText ?? $this->label());

        return $this;
    }

    public function modal(): ?ConfirmActionModal
    {
        return $this->modal;
    }
}
