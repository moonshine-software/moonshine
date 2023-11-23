<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\Fields\Fields;

trait WithSpecialFields
{
    protected array $specialFields = [];

    /**
     * @throws Throwable
     */
    public function getSpecialFields(): Fields
    {
        return Fields::make($this->specialFields);
    }

    public function hasSpecialFields(): bool
    {
        return $this->getSpecialFields()->isNotEmpty();
    }
}