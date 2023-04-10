<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\Fields\Field;

/**
 * @mixin Field
 */
trait WithResourceMode
{
    protected bool $resourceMode = false;

    public function resourceMode(): static
    {
        $this->resourceMode = true;

        abort_if(
            ! $this->resource(),
            500,
            'Resource required for resourceMode'
        );

        return $this;
    }

    public function isResourceMode(): bool
    {
        return $this->resourceMode;
    }
}
