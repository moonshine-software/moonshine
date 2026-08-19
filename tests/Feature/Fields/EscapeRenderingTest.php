<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

uses()->group('fields');
uses()->group('escape');

const XSS_TAG = '<script>alert(1)</script>';
const XSS_ATTR = '" onfocus="alert(1)" autofocus x="';

beforeEach(function (): void {
    $fields = [
        ID::make(),
        Text::make('Name title', 'name'),
        Textarea::make('Content', 'content'),
    ];

    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestIndexFields($fields)
        ->setTestDetailFields($fields)
        ->setTestFormFields($fields)
        ->setTestExportFields([ID::make()])
        ->setTestImportFields([ID::make()]);

    // Values as stored once escaping is off the save path: exactly what was
    // typed, no entities.
    $this->item = createItem(1, 0);
    $this->item->forceFill([
        'name' => XSS_TAG . XSS_ATTR,
        'content' => XSS_TAG,
    ])->save();
});

it('never emits a raw script tag from a stored value', function (string $urlMethod): void {
    $url = $urlMethod === 'getIndexPageUrl'
        ? $this->resource->getIndexPageUrl()
        : $this->resource->{$urlMethod}($this->item->getKey());

    $html = asAdmin()->get($url)->assertOk()->getContent();

    expect($html)
        ->not->toContain(XSS_TAG)
        ->not->toContain(XSS_ATTR)
        ->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
})->with([
    ['getIndexPageUrl'],
    ['getDetailPageUrl'],
    ['getFormPageUrl'],
]);

it('stores what the form posted, byte for byte', function (): void {
    $typed = 'musique d\'ambiance & "quotes"';

    asAdmin()->put(
        $this->resource->getRoute('crud.update', $this->item->getKey()),
        ['name' => $typed, 'content' => $typed]
    );

    $this->item->refresh();

    expect($this->item->name)->toBe($typed)
        ->and($this->item->content)->toBe($typed);
});
