<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\UI\ApplyContract;
use MoonShine\Contracts\UI\FieldContract;

interface AppliesRegisterContract
{
    public function type(string $type): static;

    public function for(string $for): static;

    public function getFor(): string;

    public function defaultFor(string $for): static;

    public function getDefaultFor(): string;

    public function findByField(
        FieldContract $field,
        string $type = 'fields',
        ?string $for = null
    ): ?ApplyContract;

    public function add(string $fieldClass, string $applyClass): static;

    public function push(array $data): static;

    public function get(string $fieldClass, ?ApplyContract $default = null): ?ApplyContract;
}
