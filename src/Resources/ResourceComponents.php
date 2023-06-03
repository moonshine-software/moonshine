<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Support\Collection;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\DetailComponents\DetailComponent;
use MoonShine\FormComponents\FormComponent;

final class ResourceComponents extends Collection
{
    public function formComponents(): self
    {
        return $this->filter(
            static fn (ResourceRenderable $component
            ): bool => $component instanceof FormComponent
        )->values();
    }

    public function detailComponents(): self
    {
        return $this->filter(
            static fn (ResourceRenderable $component
            ): bool => $component instanceof DetailComponent
        )->values();
    }
}
