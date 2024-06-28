<?php

declare(strict_types=1);

it('name to initials, one word', function (): void {
    expect(\MoonShine\Utils::nameToInitials('moonshine'))
        ->toBe('MO');
});

it('name to initials, two words', function (): void {
    expect(\MoonShine\Utils::nameToInitials('beautiful moonshine'))
        ->toBe('BM');
});

it('name to initials, three words', function (): void {
    expect(\MoonShine\Utils::nameToInitials('moonshine is beautiful'))
        ->toBe('MB');
});
