<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface EntityBuilderContract
{
    public function build(): EntityContract;
}
