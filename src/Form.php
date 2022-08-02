<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Iterator;
use JsonSerializable;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Stringable;

class Form implements Stringable, Iterator, JsonSerializable
{
    use Makeable, WithComponentAttributes;

    /**
     * @var array<Field>
     */
    protected array $fields;

    protected array $values = [];

    protected string $action = '';

    protected string $method = 'POST';

    protected string $enctype = 'multipart/form-data';

    protected int $position = 0;

    /**
     * @param  array  $fields
     */
    public function __construct(array $fields)
    {
        $this->attributes = ['action', 'method', 'enctype'];
        $this->fields = $fields;
    }

    public function action(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function method(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function fill(array $values): static
    {
        $this->values = $values;

        $fields = [];

        # TODO Рекурсивно
        foreach ($this->fields() as $index => $field) {
            if ($field instanceof HasFields && $field->hasFields()) {
                $childFields = [];

                foreach ($field->getFields() as $child) {
                    $childFields[] = $child->setValue($this->values[$field->field()][$child->field()] ?? null);
                }

                $field->fields($childFields);
            }

            $fields[$index] = $field->setValue($this->values[$field->field()] ?? null);
        }

        $this->fields = $fields;

        return $this;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function values()
    {
        return $this->values;
    }

    public function render()
    {
        return view('moonshine::form.form', [
            'form' => $this,
        ]);
    }

    public function current()
    {
        return $this->fields[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->fields[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function jsonSerialize()
    {
        return [
            'attributes' => $this->attributes(),
            'fields' => $this->fields(),
            'values' => $this->values()
        ];
    }
}
