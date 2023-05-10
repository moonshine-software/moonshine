<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Field;
use Throwable;

/**
 * @mixin Field
 */
trait WithResourceMode
{
    protected bool $resourceMode = false;

    /**
     * @throws Throwable
     */
    public function resourceMode(): static
    {
        $this->resourceMode = true;

        throw_if(
            ! $this->resource(),
            FieldException::class,
            'Resource required for resourceMode'
        );

        return $this;
    }

    public function isResourceMode(): bool
    {
        return $this->resourceMode;
    }
}
