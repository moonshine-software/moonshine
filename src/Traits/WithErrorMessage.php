<?php

namespace MoonShine\Traits;

trait WithErrorMessage
{
    protected string $errorMessage = '';

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function errorMessage(string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
}