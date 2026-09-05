<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use Throwable;

/**
 * @template TFields of FieldsContract = FieldsContract
 * @mixin ComponentContract
 */
trait WithFields
{
    /**
     * @var iterable<array-key, ComponentContract>|(Closure(static): iterable<array-key, ComponentContract>)
     */
    protected iterable|Closure $fields = [];

    /** @var TFields|null */
    protected ?FieldsContract $preparedFields = null;

    public function resetPreparedFields(): static
    {
        $this->preparedFields = null;

        return $this;
    }

    /**
     * @throws Throwable
     * @return TFields
     */
    public function getPreparedFields(): FieldsContract
    {
        if (! \is_null($this->preparedFields)) {
            return clone $this->preparedFields;
        }

        return $this->preparedFields = $this->prepareFields();
    }

    /**
     * @return TFields
     */
    protected function prepareFields(): FieldsContract
    {
        return $this->getFields();
    }

    /**
     * @return TFields
     */
    public function getFields(): FieldsContract
    {
        /** @var TFields */
        return $this->getCore()->getFieldsCollection(
            $this->getRawFields()
        );
    }

    /**
     * @return iterable<array-key, ComponentContract>
     */
    public function getRawFields(): iterable
    {
        return value($this->fields, $this);
    }

    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    /**
     * @param  FieldsContract|(Closure(static): iterable<array-key, ComponentContract>)|iterable<array-key, ComponentContract>  $fields
     */
    public function fields(FieldsContract|Closure|iterable $fields): static
    {
        if ($this->getCore()->runningInConsole()) {
            $fields = $this->getCore()->getFieldsCollection(value($fields, $this));
            $fields = $fields->map(static fn (ComponentContract $field): ComponentContract => clone $field)
                ->all();
        }

        $this->fields = $fields instanceof FieldsContract
            ? $fields->all()
            : $fields;

        return $this;
    }

    /**
     * @param array<string, mixed> $raw
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
