<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

use Closure;

interface HasUpdateOnPreview
{
    public function isUpdateOnPreview(): bool;

    public function getUpdateOnPreviewCustomUrl(): ?Closure;

    public function getResourceUriForUpdate(): ?string;

    public function getPageUriForUpdate(): ?string;

    public function setUpdateOnPreviewUrl(Closure $url): static;
}
