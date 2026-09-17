<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Enums\TestEnumBreadcrumbLabel;
use MoonShine\Tests\Fixtures\Enums\TestEnumStatus;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Sets\UpdateOnPreviewPopover;

uses()->group('fields');

it('renders enum labels with their color and icon', function (string $source): void {
    $field = Enum::make('Status', 'name')->attach(TestEnumStatus::class);

    match ($source) {
        'enum' => $field->fill(TestEnumStatus::Active),
        'scalar' => $field->fill('active'),
        'model cast' => $field->fillData(
            (new Item(['name' => 'active']))->mergeCasts(['name' => TestEnumStatus::class])
        ),
        'formatted enum' => $field = Enum::make(
            'Status',
            'name',
            formatted: static fn (): TestEnumStatus => TestEnumStatus::Active,
        )->attach(TestEnumStatus::class)->fill(TestEnumStatus::Inactive),
        'formatted scalar' => $field = Enum::make(
            'Status',
            'name',
            formatted: static fn (): string => 'active',
        )->attach(TestEnumStatus::class)->fill(TestEnumStatus::Inactive),
    };

    expect((string) $field->preview())
        ->toContain('Active subscriber', 'badge-success', '<svg')
        ->not->toContain('Inactive subscriber', 'badge-error');
})->with(['enum', 'scalar', 'model cast', 'formatted enum', 'formatted scalar']);

it('renders multiple enum labels with their colors and icons', function (): void {
    $field = Enum::make('Status', 'status')
        ->attach(TestEnumStatus::class)
        ->multiple()
        ->fill(collect(['active', 'inactive']));

    $html = (string) $field->preview();

    expect($html)->toContain('Active subscriber', 'Inactive subscriber', 'badge-success', 'badge-error')
        ->and(substr_count($html, '<svg'))->toBe(2);
});

it('renders enum labels without badge methods', function (): void {
    $field = Enum::make('Color')->attach(TestEnumBreadcrumbLabel::class)->fill(TestEnumBreadcrumbLabel::Blue);

    expect((string) $field->preview())->toBe('Blue label');
});

it('renders an empty preview for a nullable enum', function (): void {
    $field = Enum::make('Status')->attach(TestEnumStatus::class)->nullable()->fill(null);

    expect((string) $field->preview())->toBe('');
});

it('still converts enum labels for other fields', function (): void {
    $field = Text::make('Status')->fill(TestEnumStatus::Active);

    expect(strip_tags((string) $field->preview()))->toBe('Active subscriber');
});

it('renders enum labels in the inline editing popover', function (): void {
    $field = Enum::make('Status')->attach(TestEnumStatus::class)->fill(TestEnumStatus::Active);

    $popover = (new UpdateOnPreviewPopover($field, 'Save', 'items', '/update-status'))();

    expect((string) $popover)->toContain('Active subscriber');
});
