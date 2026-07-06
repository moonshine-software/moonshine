<?php

declare(strict_types=1);

use MoonShine\Support\VO\DisplayValue;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;
use MoonShine\Tests\Fixtures\Enums\TestEnumLabeled;
use MoonShine\Tests\Fixtures\Enums\TestEnumUnit;

uses()->group('support');

function displayValue(mixed $value): string
{
    return (new DisplayValue($value))->__toString();
}

it('resolves a plain string value', function (): void {
    expect(displayValue('Hello'))->toBe('Hello');
});

it('casts a scalar value to string', function (): void {
    expect(displayValue(42))->toBe('42');
});

it('resolves a backed enum by its backing value', function (): void {
    expect(displayValue(TestEnumColor::Red))->toBe('R');
});

it('resolves a backed string enum by its value', function (): void {
    expect(displayValue(TestEnumLabeled::Web))->toBe('web-platform');
});

it('resolves a pure (non-backed) enum by its name', function (): void {
    expect(displayValue(TestEnumUnit::Active))->toBe('Active');
});

it('resolves a stringable value', function (): void {
    expect(displayValue(str('World')))->toBe('World');
});

it('resolves an object with __toString', function (): void {
    $value = new class () {
        public function __toString(): string
        {
            return 'stringable object';
        }
    };

    expect(displayValue($value))->toBe('stringable object');
});

it('returns an empty string for a null value', function (): void {
    expect(displayValue(null))->toBe('');
});

it('returns an empty string for a non-stringable object', function (): void {
    expect(displayValue(new stdClass()))->toBe('');
});
