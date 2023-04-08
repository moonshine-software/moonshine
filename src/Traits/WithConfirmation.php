<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

trait WithConfirmation
{
    protected bool $confirmation = false;

    public function confirmation(): bool
    {
        return $this->confirmation;
    }

    public function withConfirm(): self
    {
        $this->confirmation = true;

        return $this;
    }
}
