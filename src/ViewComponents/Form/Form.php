<?php

declare(strict_types=1);

namespace Leeto\MoonShine\ViewComponents\Form;

use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\ViewComponents\MoonShineViewComponent;

final class Form extends MoonShineViewComponent
{
    protected static string $component = 'FormComponent';

    /**
     * @var Fields<Field|Decoration>
     */
    protected Fields $fields;

    protected ?ValueEntityContract $values = null;

    protected string $action = '';

    protected string $method = 'POST';

    protected string $enctype = 'multipart/form-data';

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

    public function fill(ValueEntityContract $values): static
    {
        $this->values = $values;
        $this->fields = $this->fields()->fillValues($values);

        return $this;
    }

    public function fields(): Fields
    {
        return $this->fields;
    }

    public function values(): ?ValueEntityContract
    {
        return $this->values;
    }

    public function jsonSerialize(): array
    {
        return [
            ...parent::jsonSerialize(),

            'fields' => $this->fields(),
        ];
    }
}
