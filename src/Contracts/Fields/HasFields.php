<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Fields;

use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;

interface HasFields
{
    /**
     * @param  array<Field>  $fields
     * @return $this
     */
    public function fields(array $fields): static;

    /**
     * @return Fields
     */
    public function getFields(): Fields;

    public function hasFields(): bool;
}
