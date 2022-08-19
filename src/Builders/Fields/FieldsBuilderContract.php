<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Builders\Fields;

use Leeto\MoonShine\Fields\Field;

interface FieldsBuilderContract
{
    public function showOrHide(): static;

    public function applyParent(): static;

    public function setRelatedValues(): static;

    public function setValue(): static;

    public function make(): Field;
}
