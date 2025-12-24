<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs;

use InvalidArgumentException;

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
        if ($this->isInvalidValueForOperator()) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination.'
            );
        }

        if ($this->isInvalidOperator()) {
            $this->value = $this->operator;
            $this->operator = '=';
        }

        if (! \is_array($value) && \in_array($operator, $this->arrayOperators)) {
            throw new InvalidArgumentException(
                'Illegal operator and value combination. Value must be array type'
            );
        }
    }

    protected function isInvalidValueForOperator(): bool
    {
        return \is_null($this->value) && \in_array($this->operator, $this->operators) &&
               ! \in_array($this->operator, ['=', '!=']);
    }

    protected function isInvalidOperator(): bool
    {
        return ! \is_string($this->operator) || (! \in_array(
            strtolower($this->operator),
            $this->operators,
            true
        ));
    }
}
