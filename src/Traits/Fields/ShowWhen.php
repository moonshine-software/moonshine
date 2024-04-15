<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use InvalidArgumentException;
use MoonShine\Contracts\Fields\HasFields;

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

    public function showWhenCondition(): array
    {
        return $this->showWhenCondition;
    }

    public function showWhen(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static {
        $this->showWhenData = $this->makeCondition(...func_get_args());
        [$column, $value, $operator] = $this->showWhenData;
        $this->showWhenState = true;

        $name = $this->name();

        if($this instanceof HasFields) {
            $name = str_replace('[]', '', $name);
        }

        $this->showWhenCondition[] = [
            'object_id' => spl_object_id($this),
            'showField' => $name,
            'changeField' => $this->dotNestedToName($column),
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
        if(func_num_args() === 2) {
            $value = $operator;
        }

        if(is_array($value)) {
            foreach ($value as $key => $item) {
                // Casting to Date type for javascript
                $value[$key] = strtotime((string) $item) * 1000;
            }
        } else {
            $value = strtotime((string) $value) * 1000;
        }

        if(func_num_args() === 2) {
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

        if ($this->invalidOperatorAndValue($operator, $value)) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination.'
            );
        }

        if ($this->invalidOperator($operator)) {
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

    protected function invalidOperatorAndValue($operator, $value): bool
    {
        return is_null($value) && in_array($operator, $this->operators) &&
            ! in_array($operator, ['=', '!=']);
    }

    protected function invalidOperator(mixed $operator): bool
    {
        return ! is_string($operator) || (! in_array(
            strtolower($operator),
            $this->operators,
            true
        ));
    }
}
