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

    public function getFromAttributes(): ComponentAttributeBag
    {
        return $this->fromAttributes ?? $this->attributes();
    }

    public function toAttributes(array $attributes): static
    {
        $this->toAttributes = $this->attributes()->merge($attributes);

        return $this;
    }

    public function getToAttributes(): ComponentAttributeBag
    {
        return $this->toAttributes ?? $this->attributes();
    }

    public function fromTo(string $fromField, string $toField): static
    {
        $this->fromField = $fromField;
        $this->toField = $toField;

        return $this;
    }
}
