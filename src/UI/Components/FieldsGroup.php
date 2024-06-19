<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use MoonShine\UI\Fields\Field;
use Throwable;

final class FieldsGroup extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.fields-group';

    /**
     * @throws Throwable
     */
    public function previewMode(): self
    {
        return $this->mapFields(
            static fn (Field $field): Field => $field->forcePreview()
        );
    }

    /**
     * @throws Throwable
     */
    public function fill(array $raw = [], mixed $casted = null, int $index = 0): self
    {
        return $this->mapFields(
            static fn (Field $field): Field => $field->fillData(is_null($casted) ? $raw : $casted, $index)
        );
    }

    /**
     * @throws Throwable
     */
    public function withoutWrappers(): self
    {
        return $this->mapFields(
            static fn (Field $field): Field => $field->withoutWrapper()
        );
    }

    /**
     * @param  Closure(Field $field): Field  $callback
     * @throws Throwable
     */
    public function mapFields(Closure $callback): self
    {
        $this->getComponents()
            ->onlyFields()
            ->map(static fn (Field $field): Field => $callback($field));

        return $this;
    }
}
