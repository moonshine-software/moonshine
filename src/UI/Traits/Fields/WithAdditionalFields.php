<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\UI\Collections\Fields;
use Throwable;

trait WithAdditionalFields
{
    protected array $additionalFields = [];

    /**
     * @throws Throwable
     */
    public function getAdditionalFields(): Fields
    {
        return fieldsCollection($this->additionalFields);
    }

    /**
     * @throws Throwable
     */
    public function hasAdditionalFields(): bool
    {
        return $this->getAdditionalFields()->isNotEmpty();
    }
}
