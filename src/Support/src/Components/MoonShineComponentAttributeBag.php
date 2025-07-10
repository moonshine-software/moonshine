<?php

declare(strict_types=1);

namespace MoonShine\Support\Components;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;

final class MoonShineComponentAttributeBag extends ComponentAttributeBag implements ComponentAttributesBagContract
{
    public function concat(string $name, string $value, string $separator = ' '): void
    {
        $old = $this->attributes[$name] ?? '';

        if (! \is_string($old)) {
            $old = '';
        }

        $this->attributes[$name] = $this->unique($old, $value, $separator);
    }

    public function set(string $name, string|bool $value): void
    {
        $this->attributes[$name] = $value;
        $this->attributes = $this
            ->merge([$name => $value])
            ->getAttributes();
    }

    public function remove(string $name): void
    {
        $this->attributes = $this
            ->filter(static fn (mixed $value, string $attrName): bool => $attrName !== $name)
            ->getAttributes();
    }

    private function unique(string $old, string $new, string $separator = ' '): string
    {
        return Str::of($old)
            ->append($separator)
            ->append($new)
            ->trim($separator)
            ->explode($separator)
            ->unique()
            ->implode($separator);
    }
}
