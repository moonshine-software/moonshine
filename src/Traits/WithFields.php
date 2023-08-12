<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use Throwable;

/**
 * @mixin MoonShineRenderable
 */
trait WithFields
{
    protected array $fields = [];

    /**
     * @return Fields<Field>
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        return Fields::make($this->fields);
    }

    public function hasFields(): bool
    {
        return count($this->fields) > 0;
    }

    /**
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
