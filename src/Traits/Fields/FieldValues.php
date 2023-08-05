<?php

namespace MoonShine\Traits\Fields;

use MoonShine\Fields\Fields;
use Throwable;

trait FieldValues
{
    protected array $fields = [];

    protected array $values = [];

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        $fields = Fields::make($this->fields);
        $fields->fillValues(
            $this->getValues(),
            $this->getCastedData()
        );

        return $fields;
    }

    public function fill(array $values = []): self
    {
        $this->values = $values;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}