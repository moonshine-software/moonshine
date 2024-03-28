<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithVersion
{
    private string|int|null $version = null;

    public function version(string|int|null $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): int|string|null
    {
        return $this->version;
    }
}
