<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Support\DTOs\ShowWhenCondition;
use MoonShine\UI\Contracts\RangeFieldContract;

trait ShowWhen
{
    public bool $showWhenState = false;

    /**
     * @var array<array-key, ShowWhenCondition>
     */
    protected array $showWhenCondition = [];

    public function hasShowWhen(): bool
    {
        return $this->showWhenState;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getShowWhenCondition(): array
    {
        $data = [];

        foreach ($this->showWhenCondition as $condition) {
            if ($this instanceof RangeFieldContract) {
                $data[] = $this->showWhenConditionToArray($condition, $this->getFromField(), 'from');
                $data[] = $this->showWhenConditionToArray($condition, $this->getToField(), 'to');
            } else {
                $data[] = $this->showWhenConditionToArray($condition);
            }
        }

        return $data;
    }

    public function showWhen(
        string $column,
        mixed $operator = null,
        mixed $value = null,
    ): static {
        $this->showWhenState = true;
        $condition = $this->makeCondition(
            ...\func_get_args(),
        );

        $this->showWhenCondition[] = $condition;

        return $this;
    }

    public function showWhenRow(
        string $column,
        mixed $operator = null,
        mixed $value = null,
    ): static {
        $this->showWhenState = true;

        $condition = $this->makeCondition(
            ...\func_get_args(),
        );

        $condition->isRowMode = true;

        $this->showWhenCondition[] = $condition;

        return $this;
    }

    public function showWhenDate(
        string $column,
        mixed $operator = null,
        mixed $value = null,
    ): static {
        if (\func_num_args() === 2) {
            $value = $operator;
        }

        if (\is_array($value)) {
            foreach ($value as $key => $item) {
                // Casting to Date type for JavaScript
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

    /**
     * @return array<string, mixed>
     */
    protected function showWhenConditionToArray(
        ShowWhenCondition $condition,
        ?string $nameIndex = null,
        ?string $rangeType = null,
    ): array {
        $data = [
            'object_id' => (string) spl_object_id($this),
            'showField' => $this->getAttribute('data-show-when-field') ?? $this->getNameAttribute($nameIndex),
            'changeField' => $this->getDotNestedToName($condition->column),
            'operator' => $condition->operator,
            'value' => $condition->value,
            'is_row_mode' => $condition->isRowMode,
        ];

        if ($rangeType) {
            $data['range_type'] = $rangeType;
        }

        return $data;
    }

    protected function makeCondition(
        string $column,
        mixed $operator = null,
        mixed $value = null,
        bool $isRowMode = false,
    ): ShowWhenCondition {
        return new ShowWhenCondition(
            $column,
            \func_num_args() === 2 ? '=' : $operator,
            \func_num_args() === 2 ? $operator : $value,
            isRowMode: $isRowMode,
        );
    }
}
