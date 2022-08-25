<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits;

use Leeto\MoonShine\Fields\Fields;

trait WithFields
{
    protected array $fields = [];

    public function getFields(): Fields
    {
        return Fields::make($this->fields);
    }

    public function hasFields(): bool
    {
        return $this->getFields()->isNotEmpty();
    }

    /**
     * @param  array  $fields
     * @return $this
     */
    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
