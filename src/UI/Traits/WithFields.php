<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\MoonShineRenderable;
use Throwable;

/**
 * @template T of FieldsCollection
 * @mixin MoonShineRenderable
 */
trait WithFields
{
    protected array|Closure $fields = [];

    /**
     * @throws Throwable
     * @return Fields<T>
     */
    public function getPreparedFields(): FieldsCollection
    {
        return $this->getFields();
    }

    /**
     * @throws Throwable
     * @return Fields<T>
     */
    public function getFields(): FieldsCollection
    {
        return fieldsCollection(
            $this->getRawFields()
        );
    }

    public function getRawFields(): array
    {
        return value($this->fields, $this);
    }

    /**
     * @throws Throwable
     */
    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    /**
     * @param  Fields<T>|Closure|array  $fields
     */
    public function fields(FieldsCollection|Closure|array $fields): static
    {
        if($fields instanceof Closure) {
            $fields = $fields();
        }

        if(moonshine()->runningInConsole()) {
            $fields = collect($fields)
                ->map(static fn (object $field): object => clone $field)
                ->toArray();
        }

        $this->fields = $fields instanceof FieldsCollection
            ? $fields->toArray()
            : $fields;

        return $this;
    }

    /**
     * @throws Throwable
     * @return Fields<T>
     */
    protected function getFilledFields(
        array $raw = [],
        mixed $casted = null,
        int $index = 0,
        ?FieldsCollection $preparedFields = null
    ): FieldsCollection {
        $fields = $preparedFields ?? $this->getFields();

        return $fields->fillCloned($raw, $casted, $index, $fields);
    }
}
