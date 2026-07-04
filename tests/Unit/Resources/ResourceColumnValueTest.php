<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Enums\TestEnumColor;
use MoonShine\Tests\Fixtures\Enums\TestEnumLabeled;
use MoonShine\Tests\Fixtures\Resources\TestResource;

uses()->group('resources');
uses()->group('crud-resources');

function columnResource(string $column): TestResource
{
    return app(TestResource::class)->setTestColumn($column);
}

it('resolves a plain string column value', function (): void {
    expect(columnResource('name')->getColumnValue(['name' => 'Hello']))
        ->toBe('Hello');
});

it('casts scalar column values to string', function (): void {
    expect(columnResource('id')->getColumnValue(['id' => 42]))
        ->toBe('42');
});

it('resolves a backed enum column value by its backing value', function (): void {
    expect(columnResource('color')->getColumnValue(['color' => TestEnumColor::Red]))
        ->toBe('R');
});

it('prefers the enum toString() convention when available', function (): void {
    expect(columnResource('type')->getColumnValue(['type' => TestEnumLabeled::Web]))
        ->toBe('Web platform');
});

it('resolves a stringable column value', function (): void {
    expect(columnResource('name')->getColumnValue(['name' => str('World')]))
        ->toBe('World');
});

it('resolves an object with __toString', function (): void {
    $value = new class () {
        public function __toString(): string
        {
            return 'stringable object';
        }
    };

    expect(columnResource('name')->getColumnValue(['name' => $value]))
        ->toBe('stringable object');
});

it('returns an empty string for a null column value', function (): void {
    expect(columnResource('name')->getColumnValue(['name' => null]))
        ->toBe('');
});

it('returns an empty string for a non-stringable object', function (): void {
    expect(columnResource('name')->getColumnValue(['name' => new stdClass()]))
        ->toBe('');
});
