<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\View\ComponentAttributeBag;

trait RangeTrait
{
    public string $fromField = 'from';

    public string $toField = 'to';

    protected ?ComponentAttributeBag $fromAttributes = null;

    protected ?ComponentAttributeBag $toAttributes = null;

    public function fromAttributes(array $attributes): static
    {
        $this->fromAttributes = $this->attributes()->merge($attributes);

        return $this;
    }

    protected function reformatAttributes(
        ?ComponentAttributeBag $attributes = null,
        string $name = ''
    ): ComponentAttributeBag {
        $dataName = $this->attributes()->get('data-name');

        return ($attributes ?? $this->attributes())
            ->except(['data-name'])
            ->when(
                $dataName,
                fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                    'data-name' => str($dataName)->replaceLast('[]', "[$name]"),
                ])
            );
    }

    public function getFromAttributes(): ComponentAttributeBag
    {
        return $this->reformatAttributes($this->fromAttributes, $this->fromField);
    }

    public function toAttributes(array $attributes): static
    {
        $this->toAttributes = $this->attributes()->merge($attributes);

        return $this;
    }

    public function getToAttributes(): ComponentAttributeBag
    {
        return $this->reformatAttributes($this->toAttributes, $this->toField);
    }

    public function fromTo(string $fromField, string $toField): static
    {
        $this->fromField = $fromField;
        $this->toField = $toField;

        return $this;
    }
}
