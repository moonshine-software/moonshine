<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Modals\ConfirmActionModal;
use MoonShine\Modals\Modal;

trait WithModal
{
    protected bool $isConfirmed = false;

    protected ?Modal $modal = null;

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function isInModal(): bool
    {
        return !is_null($this->modal);
    }

    public function inModal(
        string $title = null,
        string $content = null,
        string $confirmButtonText = null
    ): self {
        $this->isConfirmed = true;

        $this->modal = ConfirmActionModal::make($title, $content)
            ->confirmButtonText($confirmButtonText ?? $this->label());

        return $this;
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

    public function modal(): ?Modal
    {
        return $this->modal;
    }
}
