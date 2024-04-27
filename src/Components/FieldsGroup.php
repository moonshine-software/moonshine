<?php

declare(strict_types=1);

namespace MoonShine\Components;

use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use Throwable;

final class FieldsGroup extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.fields-group';

    /**
     * @throws Throwable
     */
    public function previewMode(): self
    {
        $this->getComponents()
            ->onlyFields()
            ->map(fn (Field $field): Field => $field->forcePreview());

        return $this;
    }
}
