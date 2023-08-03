<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

class HasOne extends HasMany
{
    protected string $view = 'moonshine::fields.relationships.has-one';

    protected function resolvePreview(): string
    {
        if (is_null($this->toValue())) {
            return '';
        }

        $this->setValue(
            collect([
                $this->toValue(),
            ])
        );

        return parent::resolvePreview();

        $value = $this->toValue();
        $column = $this->getResource()->column();

        if ($value) {
            return (string) $value->{$column};
        }

        return '';
    }
}
