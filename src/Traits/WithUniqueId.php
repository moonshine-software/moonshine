<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\Support\Str;

trait WithUniqueId
{
    public function id(string $index = null): string
    {
        return (string) str(Str::random())
            ->slug('_')
            ->when(! is_null($index), fn ($str) => $str->append('_' . $index));
    }
}
