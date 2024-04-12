<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\Components\Layout\WithComponents;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use Throwable;

final class FieldsGroup extends WithComponents
{
    protected string $view = 'moonshine::components.fields-group';

    /**
     * @throws Throwable
     */
    public function previewMode(): self
    {
        if(!$this->components instanceof Fields) {
            $this->components = Fields::make($this->components);
        }

        $this->components
            ->onlyFields()
            ->map(fn(Field $field): Field => $field->forcePreview());

        return $this;
    }
}
