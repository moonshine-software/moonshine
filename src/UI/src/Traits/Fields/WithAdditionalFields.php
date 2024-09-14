<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use Throwable;

trait WithAdditionalFields
{
    protected array $additionalFields = [];

    /**
     * @throws Throwable
     */
    public function getAdditionalFields(): FieldsContract
    {
        return $this->getCore()->getFieldsCollection($this->additionalFields);
    }

    /**
     * @throws Throwable
     */
    public function hasAdditionalFields(): bool
    {
        return $this->getAdditionalFields()->isNotEmpty();
    }
}
