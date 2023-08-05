<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use MoonShine\Fields\Field;
use Throwable;

/**
 * @mixin Field
 */
trait WithJsonValues
{
    /**
     * @throws Throwable
     */
    public function jsonValues(): array
    {
        # TODO[refactor]
        return $this->value()
            ->rows()
            ->map(fn ($row) => $row->getFields()->getValues()->toArray())->toArray();
    }
}
