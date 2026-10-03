<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestFileResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('model-relation-fields');

it('preserves explicit relation label preferences in modal buttons', function (string $relation, bool $escape, bool $form): void {
    moonshine()->getConfig()->escapeLabel(! $escape);
    $item = createItem(countComments: 1);

    if ($relation === 'one') {
        $item->itemFile()->create(['path' => 'example.txt']);
    }

    $field = $relation === 'many'
        ? HasMany::make('<b>Related items</b>', 'comments', resource: TestCommentResource::class)
        : HasOne::make('<b>Related items</b>', 'itemFile', resource: TestFileResource::class);

    $field->escapeLabel($escape)->modalMode(
        modifyButton: static fn (ActionButtonContract $button): ActionButtonContract => $button->customAttributes([
            'data-test' => 'relation-modal-button',
        ]),
    );

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([$field]);
    $url = $form ? $resource->getFormPageUrl($item->getKey()) : $resource->getIndexPageUrl();
    $response = asAdmin()->get($url)->assertOk();

    $document = new DOMDocument();
    @$document->loadHTML($response->getContent());
    $xpath = new DOMXPath($document);
    $buttons = $xpath->query('//a[@data-test="relation-modal-button"]');

    expect($buttons->length)->toBeGreaterThan(0);
    expect($xpath->query('.//b', $buttons->item(0))->length)->toBe($escape ? 0 : 1);
    expect(trim($buttons->item(0)->textContent))->toBe($escape ? '<b>Related items</b>' : 'Related items');
})->with(['one', 'many'])->with([true, false])->with([true, false]);
