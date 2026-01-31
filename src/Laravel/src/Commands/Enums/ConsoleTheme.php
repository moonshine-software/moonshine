<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands\Enums;

enum ConsoleTheme: string
{
    case Purple = 'purple';
    case Golden = 'golden';
    case Twilight = 'twilight';
    case Moonlight = 'moonlight';
    case Sunset = 'sunset';

    /**
     * @return array<int, int>
     */
    public function gradient(): array
    {
        return match ($this) {
            self::Purple => [135, 134, 133, 97, 61, 55],
            self::Golden => [220, 214, 178, 172, 136, 100],
            self::Twilight => [141, 105, 69, 63, 57, 21],
            self::Moonlight => [189, 153, 147, 141, 135, 129],
            self::Sunset => [214, 208, 172, 136, 100, 94],
        };
    }

    public function primary(): int
    {
        return $this->gradient()[0];
    }

    public function accent(): int
    {
        return $this->gradient()[2];
    }

    public static function random(): self
    {
        $cases = self::cases();

        return $cases[array_rand($cases)];
    }
}
