<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface ValueEntityBuilderContract
{
    public function build(): ValueEntityContract;
}
