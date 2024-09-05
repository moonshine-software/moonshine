<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeBool;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\DefaultValueTypes\MustBeNull;
use UnitEnum;

trait WithDefaultValue
{
    protected mixed $default = null;

    public function default(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getDefault(): mixed
    {
        if ($this instanceof MustBeNull) {
            return null;
        }

        if (is_array($this->default) && $this instanceof CanBeArray) {
            return $this->default;
        }

        if (is_bool($this->default) && $this instanceof CanBeBool) {
            return $this->default;
        }

        if (is_numeric(
            $this->default
        ) && $this instanceof CanBeNumeric) {
            return $this->default;
        }

        if (is_string($this->default) && $this instanceof CanBeString) {
            return $this->default;
        }

        if (is_object($this->default) && $this instanceof CanBeObject) {
            return $this->default;
        }

        if ($this->default instanceof UnitEnum) {
            return $this->default;
        }

        return null;
    }
}
