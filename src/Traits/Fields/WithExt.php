<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait WithExt
{
    protected ?string $ext = null;

    public function expansion(string $expansion): static
    {
        $this->ext = $expansion;

        return $this;
    }

    public function hasExt(): bool
    {
        return !is_null($this->ext);
    }

    public function ext(): ?string
    {
        return $this->ext;
    }
}
