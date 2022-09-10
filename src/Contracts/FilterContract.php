<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

use JsonSerializable;
use Leeto\MoonShine\Fields\Fields;

interface FilterContract extends JsonSerializable
{
    public function label(): string;

    public function queryCallback(...$args);

    public function fields(): Fields;
}
