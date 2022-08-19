<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Builders\Fields;

class FieldsBuilder
{
    public function build(FieldsBuilderContract $field): FieldsBuilderContract
    {
        # TODO ???
        return $field->showOrHide()
            ->applyParent()
            ->setRelatedValues()
            ->setValue();
    }
}
