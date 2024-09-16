<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use Throwable;

/**
 * @template T of FieldsContract
 */
trait WithAdditionalFields
{
    protected array $additionalFields = [];

    /**
     * @return T
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
