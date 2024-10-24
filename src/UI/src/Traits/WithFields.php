<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use Throwable;

/**
 * @mixin ComponentContract
 */
trait WithFields
{
    protected iterable|Closure $fields = [];

    protected ?FieldsContract $preparedFields = null;

    public function resetPreparedFields(): static
    {
        $this->preparedFields = null;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getPreparedFields(): FieldsContract
    {
        if (! \is_null($this->preparedFields)) {
            return clone $this->preparedFields;
        }

        return $this->preparedFields = $this->prepareFields();
    }

    protected function prepareFields(): FieldsContract
    {
        return $this->getFields();
    }

    public function getFields(): FieldsContract
    {
        return $this->getCore()->getFieldsCollection(
            $this->getRawFields()
        );
    }

    public function getRawFields(): iterable
    {
        return value($this->fields, $this);
    }

    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    public function fields(FieldsContract|Closure|iterable $fields): static
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
     * @throws Throwable
     */
    protected function getFilledFields(
        array $raw = [],
        ?DataWrapperContract $casted = null,
        int $index = 0,
        ?FieldsContract $preparedFields = null
    ): FieldsContract {
        $fields = $preparedFields ?? $this->getFields();

        return $fields->fillCloned($raw, $casted, $index, $fields);
    }
}
