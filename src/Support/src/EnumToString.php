<?php

declare(strict_types=1);

namespace MoonShine\Support;

use UnitEnum;
use BackedEnum;
use Stringable;

final readonly class EnumToString implements Stringable
{
    public function __construct(private mixed $value) {}

    public function convert(): mixed
    {
        if(!$this->value instanceof UnitEnum) {
            return $this->value;
        }

        if(method_exists($this->value, 'toString')) {
            return $this->value->toString();
        }

        return (string) ($this->value instanceof BackedEnum ? $this->value->value : $this->value->name);
    }

    public function __toString(): string
    {
        $value = $this->convert();

        if(!is_string($value)) {
            return '';
        }

        return $value;
    }
}
