<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

use Closure;

interface HasUpdateOnPreviewContract
{
    public function isUpdateOnPreview(): bool;

    public function setUpdateOnPreviewUrl(Closure $url): static;
}
