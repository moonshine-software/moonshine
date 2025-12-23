<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs;


final class ShowWhenCondition
{
    /**
     * @var string[]
     */
    protected array $operators = [
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
    protected array $arrayOperators = [
        'in',
        'not in',
    ];

    public function __construct(
        public string $column,
        public mixed $operator,
        public mixed $value,
        public bool $isRowMode = false,
    ) {
        if ($this->isInvalidValueForOperator($operator, $value)) {
            throw new \InvalidArgumentException(
                'Illegal operator and value combination.'
            );
        }

        if ($this->isInvalidOperator($operator)) {
            $value = $operator;
            $operator = '=';
        }

        if (! \is_array($value) && \in_array($operator, $this->arrayOperators)) {
            throw new \InvalidArgumentException(
                'Illegal operator and value combination. Value must be array type'
            );
        }
    }

    protected function isInvalidValueForOperator(mixed $operator, mixed $value): bool
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
