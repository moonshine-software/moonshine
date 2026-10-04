<?php

declare(strict_types=1);

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
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

    $titles = $xpath->query('//*[contains(@class, "modal-title")][contains(., "Related items")]');

    expect($titles->length)->toBeGreaterThan(0);
    expect($xpath->query('.//b', $titles->item(0))->length)->toBe($escape ? 0 : 1);
})->with(['one', 'many'])->with([true, false])->with([true, false]);

it('escapes related item labels independently from the field label', function (string $mode, ?bool $local, bool $escaped): void {
    $item = createItem(countComments: 0);
    $item->categories()->attach(Category::factory()->create(['name' => '<b>Category</b>']));

    $field = BelongsToMany::make('<b>Categories</b>', 'categories', resource: TestCategoryResource::class)->unescapeLabel();

    match ($mode) {
        'tree' => $field->tree('category_id'),
        'horizontal' => $field->horizontalMode(),
        'inline link' => $field->inLine(link: static fn (): string => '/category'),
    };

    if ($local !== null) {
        $field->escapeOptionLabels($local);
    }

    $field->fillData($item);

    $html = match ($mode) {
        'tree' => $field->toTreeHtml(),
        'horizontal' => $field->toListHtml(),
        'inline link' => (string) $field->preview(),
    };

    expect($html)->toContain($escaped ? '&lt;b&gt;Category&lt;/b&gt;' : '<b>Category</b>');
})->with(['tree', 'horizontal', 'inline link'])->with([
    'default' => [null, true],
    'local disabled' => [false, false],
    'local enabled' => [true, true],
]);
