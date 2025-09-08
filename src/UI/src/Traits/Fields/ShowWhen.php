<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use InvalidArgumentException;
use MoonShine\UI\Contracts\RangeFieldContract;

trait ShowWhen
{
    /**
     * @var string[]
     */
    public array $operators = [
        '=',
        '<',
        '>',
        '<=',
        '>=',
        '!=',
        'in',
        'not in',
    ];

    /**
     * @var string[]
     */
    public array $arrayOperators = [
        'in',
        'not in',
    ];

    public bool $showWhenState = false;

    /**
     * @var array<array-key, mixed>
     */
    protected array $showWhenCondition = [];

    /**
     * @var array<array-key, mixed>
     */
    protected array $showWhenData = [];

    public function hasShowWhen(): bool
    {
        return $this->showWhenState;
    }

    /**
     * @return array<string, string>
     */
    public function getShowWhenCondition(): array
    {
        return $this->showWhenCondition;
    }

    /*
     * The "name" attribute in some fields may be changed after "showWhenCondition" is initialized,
     * then we have to change showField value
     */
    public function modifyShowFieldName(string $name): static
    {
        $this->showWhenCondition = array_map(function (array $item) use ($name): array {
            $item['showField'] = $name;

            return $item;
        }, $this->showWhenCondition);

        return $this;
    }

    public function modifyShowFieldRangeName(): static
    {
        if (! $this instanceof RangeFieldContract) {
            return $this;
        }

        $this->showWhenCondition = array_map(function (array $item): array {
            $item['showField'] = $item['range_type'] === 'from'
                ? $this->getNameAttribute($this->getFromField())
                : $this->getNameAttribute($this->getToField());

            return $item;
        }, $this->showWhenCondition);

        return $this;
    }

    public function showWhen(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static {
        if ($this instanceof RangeFieldContract) {
            $this->showWhenRange($column, $operator, $value);

            return $this;
        }

        $this->showWhenData = $this->makeCondition(...\func_get_args());
        [$column, $value, $operator] = $this->showWhenData;
        $this->showWhenState = true;

        $this->showWhenCondition[] = [
            'object_id' => (string) spl_object_id($this),
            'showField' => $this->getNameAttribute(),
            'changeField' => $this->getDotNestedToName($column),
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    public function showWhenDate(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static {
        if (\func_num_args() === 2) {
            $value = $operator;
        }

        if (\is_array($value)) {
            foreach ($value as $key => $item) {
                // Casting to Date type for javascript
                $value[$key] = strtotime((string) $item) * 1000;
            }
        } else {
            $value = strtotime((string) $value) * 1000;
        }

        if (\func_num_args() === 2) {
            return $this->showWhen($column, $value);
        }

        return $this->showWhen($column, $operator, $value);
    }

    protected function showWhenRange(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static {
        if (! $this instanceof RangeFieldContract) {
            return $this;
        }

        $this->showWhenData = $this->makeCondition(...\func_get_args());
        [$column, $value, $operator] = $this->showWhenData;
        $this->showWhenState = true;

        $showWhenCondition = [
            'object_id' => (string) spl_object_id($this),
            'showField' => $this->getNameAttribute($this->getFromField()),
            'changeField' => $this->getDotNestedToName($column),
            'operator' => $operator,
            'value' => $value,
            'range_type' => 'from',
        ];
        $this->showWhenCondition[] = $showWhenCondition;

        $showWhenCondition['showField'] = $this->getNameAttribute($this->getToField());
        $showWhenCondition['range_type'] = 'to';
        $this->showWhenCondition[] = $showWhenCondition;

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function makeCondition(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): array {
        return [
            $column,
            ...$this->prepareValueAndOperator(
                $value,
                $operator,
                \func_num_args() === 2
            ),
        ];
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function prepareValueAndOperator(
        mixed $value,
        mixed $operator = null,
        bool $useDefault = false
    ): array {
        if ($useDefault) {
            return [$operator, '='];
        }

        if ($this->isInvalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination.'
            );
        }

        if ($this->isInvalidOperator($operator)) {
            $value = $operator;
            $operator = '=';
        }

        if (! \is_array($value) && \in_array($operator, $this->arrayOperators)) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination. Value must be array type'
            );
        }

        return [$value, $operator];
    }

    protected function isInvalidOperatorAndValue(mixed $operator, mixed $value): bool
    {
        return \is_null($value) && \in_array($operator, $this->operators) &&
            ! \in_array($operator, ['=', '!=']);
    }

    protected function isInvalidOperator(mixed $operator): bool
    {
        return ! \is_string($operator) || (! \in_array(
            strtolower($operator),
            $this->operators,
            true
        ));
    }
}
