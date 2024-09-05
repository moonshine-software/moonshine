<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface FormContract
{
    public function __invoke(): FormBuilderContract;
}
