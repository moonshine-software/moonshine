<?php

declare(strict_types=1);

namespace Leeto\MoonShine;

use Illuminate\Contracts\View\View;
use Iterator;
use JsonSerializable;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;
use Stringable;

class DetailCard implements Stringable, Iterator, JsonSerializable
{
    use Makeable, WithComponentAttributes;

    /**
     * @var array<Field>
     */
    protected array $fields;

    protected array $values = [];

    protected int $position = 0;

    /**
     * @param  array  $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function fields(): array
    {
        return $this->fields;
    }

    public function values(): array
    {
        return $this->values;
    }

    public function render(): View
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

    public function valid(): bool
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

    public function jsonSerialize(): array
    {
        return [
            'attributes' => $this->attributes()->getAttributes(),
            'fields' => $this->fields(),
            'values' => $this->values()
        ];
    }
}
