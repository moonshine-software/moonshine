<?php

namespace MoonShine;

use Illuminate\Support\Str;

class Utils
{
    /**
     * return initials from name in UPPERCASE
     */
    public static function nameToInitials(string $name): string
    {
        $nameParts = explode(' ', $name);

        if (count($nameParts) === 1) {
            return mb_strtoupper(Str::charAt($name, 0) . (Str::charAt($name, 1) ?? ''));
        }

        return mb_strtoupper(Str::charAt($nameParts[0], 0) . Str::charAt(last($nameParts), 0));
    }
}
