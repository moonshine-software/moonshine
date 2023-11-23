<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\Fields\Fields;
use Throwable;

trait WithAdditionalFields
{
    protected array $additionalFields = [];

    /**
     * @throws Throwable
     */
    public function getAdditionalFields(): Fields
    {
        return Fields::make($this->additionalFields);
    }

    public function hasAdditionalFields(): bool
    {
        return $this->getAdditionalFields()->isNotEmpty();
    }
}
