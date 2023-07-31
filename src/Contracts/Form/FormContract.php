<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Form;

interface FormContract
{
    public function fields(array $fields): self;

    public function fill(array $values = []): self;

    public function buttons(array $buttons = []): self;
}
