<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Form;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponentAttributes;

final class Form implements JsonSerializable
{
    use Makeable;
    use WithComponentAttributes;

    /**
     * @var Fields<Field|Decoration>
     */
    protected Fields $fields;

    protected ?Model $values = null;

    protected string $action = '';

    protected string $method = 'POST';

    protected string $enctype = 'multipart/form-data';

    protected int $position = 0;

    /**
     * @param  Fields<Field|Decoration>  $fields
     */
    public function __construct(Fields $fields)
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

    public function fill(Model $values): static
    {
        $this->values = $values;
        $this->fields = $this->fields()->fillValues($values);

        return $this;
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function values(): ?Model
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return [
            'fields' => $this->fields(),
        ];
    }
}
