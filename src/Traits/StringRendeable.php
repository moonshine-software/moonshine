<?php

namespace MoonShine\Traits;

use Throwable;

trait StringRendeable
{
    /**
     * @throws Throwable
     */
    public function __toString(): string
    {
        return (string) $this->render();
    }
}