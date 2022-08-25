<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait SlideTrait
{
    public string $fromField = 'from';

    public string $toField = 'to';

    public function fromField(string $fromField): static
    {
        $this->fromField = $fromField;

        return $this;
    }

    public function toField(string $toField): static
    {
        $this->toField = $toField;

        return $this;
    }

    public function from(): string
    {
        return $this->fromField;
    }

    public function to(): string
    {
        return $this->toField;
    }
}
