<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Fields;
use Throwable;

/**
 * @mixin MoonShineRenderable
 */
trait WithFields
{
    protected array $fields = [];

    /**
     * @throws Throwable
     */
    public function preparedFields(): Fields
    {
        return $this->getFields();
    }

    /**
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        return Fields::make($this->fields);
    }

    /**
     * @throws Throwable
     */
    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    public function fields(Fields|Closure|array $fields): static
    {
        if(is_closure($fields)) {
            $fields = $fields();
        }

        $this->fields = $fields instanceof Fields
            ? $fields->toArray()
            : $fields;

        return $this;
    }
}
