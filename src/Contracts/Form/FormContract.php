<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Form;

interface FormContract
{
    public function action(string $action): self;

    public function method(string $method): self;

    public function fields(array $fields): self;

    public function fill(array $values = []): self;

    public function async(): self;

    public function precognitive(): self;

    public function submit(string $label): self;

    public function buttons(array $buttons = []): self;
}
