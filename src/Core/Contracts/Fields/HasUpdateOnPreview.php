<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts\Fields;

use Closure;

interface HasUpdateOnPreview
{
    public function isUpdateOnPreview(): bool;

    public function setUpdateOnPreviewUrl(Closure $url): static;
}
