<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

uses()->group('fields');

it('keeps popover value escaping independent of display label preferences', function (bool $unescape, bool $escapeLabel): void {
    moonshine()->getConfig()->escapeLabel($escapeLabel);
    $item = createItem(countComments: 0);
    $item->update(['name' => '<b>A & B</b>']);

    $field = Text::make('Name', 'name')->escapeLabel(! $escapeLabel)->updateInPopover('index');

    if ($unescape) {
        $field->unescape();
    }

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([$field]);
    $response = asAdmin()->get($resource->getIndexPageUrl())->assertOk();

    $document = new DOMDocument();
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    $links = $xpath->query('//span[contains(@class, "popover-trigger")]/a');

    expect($links->length)->toBeGreaterThan(0);
    expect($xpath->query('.//b', $links->item(0))->length)->toBe($unescape ? 1 : 0);
    expect(trim($links->item(0)->textContent))->toBe($unescape ? 'A & B' : '<b>A & B</b>');
    expect($xpath->query('//span[contains(@class, "popover-trigger")]//input[@name="value"]')->length)
        ->toBeGreaterThan(0);
})->with([true, false])->with([true, false]);

it('escapes popover values of fields without a value escape override', function (bool $escapeLabel): void {
    moonshine()->getConfig()->escapeLabel($escapeLabel);
    createItem(countComments: 0);

    $field = Number::make('Points', 'start_point', static fn (): string => '<b>123</b>')->updateInPopover('index');
    $resource = TestResourceBuilder::new(Item::class)->setTestFields([$field]);
    $response = asAdmin()->get($resource->getIndexPageUrl())->assertOk();

    $document = new DOMDocument();
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    $links = $xpath->query('//span[contains(@class, "popover-trigger")]/a');

    expect($links->length)->toBeGreaterThan(0);
    expect($xpath->query('.//b', $links->item(0))->length)->toBe(0);
    expect(trim($links->item(0)->textContent))->toBe('<b>123</b>');
})->with([true, false]);
