<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('fields');
uses()->group('escape');

const SWEEP_TAG = '<script>alert(1)</script>';
const SWEEP_ATTR = '" onfocus="alert(1)" autofocus x="';

beforeEach(function (): void {
    $this->resource = app(TestItemResource::class);

    $this->item = createItem(1, 1);
    $this->item->forceFill([
        'name' => SWEEP_TAG . SWEEP_ATTR,
        'content' => SWEEP_TAG,
    ])->save();
});

/**
 * Assert a response body carries no executable payload.
 *
 * JSON bodies escape the solidus, so `</script>` arrives as `<\/script>`;
 * checking only the literal would pass on a body that does leak.
 */
function assertNoRawPayload(string $body): void
{
    expect($body)
        ->not->toContain(SWEEP_TAG)
        ->not->toContain(str_replace('/', '\/', SWEEP_TAG))
        ->not->toContain(SWEEP_ATTR)
        ->not->toContain(str_replace('"', '\"', SWEEP_ATTR));
}

it('does not leak through an async component reload', function (): void {
    $html = asAdmin()->get($this->moonshineCore->getRouter()->to('component', [
        '_component_name' => 'index-table-test-item-resource',
        'resourceUri' => $this->resource->getUriKey(),
        'pageUri' => 'index-page',
    ]))->assertOk()->getContent();

    expect($html)->toContain('&lt;script&gt;');

    assertNoRawPayload($html);
});

it('does not leak through a fragment load', function (): void {
    $html = asAdmin()->get(
        $this->resource->getIndexPageUrl(['_fragment-load' => 'index-table-test-item-resource'])
    )->assertOk()->getContent();

    assertNoRawPayload($html);
});

/**
 * The SDUI structure response is a JSON API for third-party front-ends, not an
 * HTML sink. It carries the stored value verbatim -- which is the point of the
 * fix, since entity-encoded data was what #2044 reported. Escaping is the
 * consumer's responsibility at its own render step.
 *
 * This is a behaviour change: before the fix these payloads were incidentally
 * pre-escaped because the escaping happened on the save path.
 */
it('carries the stored value verbatim through the SDUI json structure', function (string $urlMethod): void {
    $url = $urlMethod === 'getIndexPageUrl'
        ? $this->resource->getIndexPageUrl()
        : $this->resource->{$urlMethod}($this->item->getKey());

    $json = asAdmin()->getJson($url, ['X-MS-Structure' => true])->assertOk()->getContent();

    expect($json)->toContain(str_replace('/', '\/', SWEEP_TAG));
})->with([
    ['getIndexPageUrl'],
    ['getDetailPageUrl'],
    ['getFormPageUrl'],
]);

it('serves the stored value verbatim to a non-blade consumer', function (): void {
    // The whole point of the fix: an API/export reading the column gets what
    // the user typed, with no entities to decode.
    expect(Item::query()->find($this->item->getKey())->name)
        ->toBe(SWEEP_TAG . SWEEP_ATTR);
});
