<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use InvalidArgumentException;

trait ShowWhen
{
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

    public array $arrayOperators = [
        'in',
        'not in',
    ];

    public bool $showWhenState = false;

    protected array $showWhenCondition = [];

    protected array $showWhenData = [];

    public function hasShowWhen(): bool
    {
        return $this->showWhenState;
    }

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
        $this->showWhenCondition = array_map(function (array $item) use ($name) {
            $item['showField'] = $name;

            return $item;
        }, $this->showWhenCondition);

        return $this;
    }

    public function showWhen(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static {
        $this->showWhenData = $this->makeCondition(...func_get_args());
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
        if (func_num_args() === 2) {
            $value = $operator;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                // Casting to Date type for javascript
                $value[$key] = strtotime((string) $item) * 1000;
            }
        } else {
            $value = strtotime((string) $value) * 1000;
        }

        if (func_num_args() === 2) {
            return $this->showWhen($column, $value);
        }

        return $this->showWhen($column, $operator, $value);
    }

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
                func_num_args() === 2
            ),
        ];
    }

    protected function prepareValueAndOperator(
        mixed $value,
        mixed $operator = null,
        $useDefault = false
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

        if (in_array($operator, $this->arrayOperators) && ! is_array($value)) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination. Value must be array type'
            );
        }

        return [$value, $operator];
    }

    protected function isInvalidOperatorAndValue($operator, $value): bool
    {
        return is_null($value) && in_array($operator, $this->operators) &&
            ! in_array($operator, ['=', '!=']);
    }

    protected function isInvalidOperator(mixed $operator): bool
    {
        return ! is_string($operator) || (! in_array(
            strtolower($operator),
            $this->operators,
            true
        ));
    }
}
