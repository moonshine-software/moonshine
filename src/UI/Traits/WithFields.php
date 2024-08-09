<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\UI\Collections\Fields;
use Throwable;

/**
 * @template T of FieldsContract
 * @mixin RenderableContract
 */
trait WithFields
{
    protected array|Closure $fields = [];

    protected ?FieldsContract $preparedFields = null;

    public function resetPreparedFields(): static
    {
        $this->preparedFields = null;

        return $this;
    }

    /**
     * @return Fields<T>
     * @throws Throwable
     */
    public function getPreparedFields(): FieldsContract
    {
        if (! is_null($this->preparedFields)) {
            return $this->preparedFields;
        }

        return $this->preparedFields = $this->prepareFields();
    }

    /**
     * @return Fields<T>
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        return $this->getFields();
    }

    /**
     * @return Fields<T>
     * @throws Throwable
     */
    public function getFields(): FieldsContract
    {
        return $this->getCore()->getFieldsCollection(
            $this->getRawFields()
        );
    }

    public function getRawFields(): array
    {
        return value($this->fields, $this) ?? [];
    }

    /**
     * @throws Throwable
     */
    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    /**
     * @param  Fields<T>|Closure(FieldsContract $ctx): list<T>|array  $fields
     */
    public function fields(FieldsContract|Closure|array $fields): static
    {
        if ($this->getCore()->runningInConsole()) {
            $fields = collect(
                value($fields, $this)
            )
                ->map(static fn (object $field): object => clone $field)
                ->toArray();
        }

        $this->fields = $fields instanceof FieldsContract
            ? $fields->toArray()
            : $fields;

        return $this;
    }

    /**
     * @return Fields<T>
     * @throws Throwable
     */
    protected function getFilledFields(
        array $raw = [],
        ?CastedDataContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): FieldsContract {
        $fields = $preparedFields ?? $this->getFields();

        return $fields->fillCloned($raw, $casted, $index, $fields);
    }
}
