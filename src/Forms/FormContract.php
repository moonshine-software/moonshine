<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\Components\FormBuilder;

interface FormContract
{
    public function __invoke(): FormBuilder;
}
