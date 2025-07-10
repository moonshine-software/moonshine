<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FieldContract;
use Throwable;

/**
 * @template T of FieldsContract
 */
trait WithAdditionalFields
{
    /**
     * @var list<FieldContract>
     */
    protected array $additionalFields = [];

    /**
     * @return T
     * @throws Throwable
     */
    protected function getAdditionalFields(): FieldsContract
    {
        return $this->getCore()->getFieldsCollection($this->additionalFields);
    }

    /**
     * @throws Throwable
     */
    protected function hasAdditionalFields(): bool
    {
        return $this->getAdditionalFields()->isNotEmpty();
    }
}
