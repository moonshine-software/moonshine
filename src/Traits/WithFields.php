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

    protected ?Closure $fieldsClosure = null;

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
    public function getFields(mixed $data = null): Fields
    {
        if(! is_null($this->fieldsClosure)) {
            $this->fields = value($this->fieldsClosure, $data, $this);
        }

        return Fields::make($this->fields);
    }

    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    public function fields(Fields|Closure|array $fields): static
    {
        if(is_closure($fields)) {
            $this->fieldsClosure = $fields;

            return $this;
        }

        $this->fields = $fields instanceof Fields
            ? $fields->toArray()
            : $fields;

        return $this;
    }
}
