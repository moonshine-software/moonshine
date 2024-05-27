<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeObject;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultMustBeNull;
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
        if ($this instanceof DefaultMustBeNull) {
            return null;
        }

        if (is_array($this->default) && $this instanceof DefaultCanBeArray) {
            return $this->default;
        }

        if (is_bool($this->default) && $this instanceof DefaultCanBeBool) {
            return $this->default;
        }

        if (is_numeric(
            $this->default
        ) && $this instanceof DefaultCanBeNumeric) {
            return $this->default;
        }

        if (is_string($this->default) && $this instanceof DefaultCanBeString) {
            return $this->default;
        }

        if (is_object($this->default) && $this instanceof DefaultCanBeObject) {
            return $this->default;
        }

        if ($this->default instanceof UnitEnum) {
            return $this->default;
        }

        return null;
    }
}
