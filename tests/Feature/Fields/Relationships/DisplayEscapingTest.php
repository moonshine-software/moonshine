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
use MoonShine\UI\Components\Link;
use MoonShine\UI\Fields\Preview;

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

it('escapes related item labels independently from the field label', function (string $mode, ?bool $local, bool $escaped, bool $global): void {
    moonshine()->getConfig()->escapeLabel($global);
    $item = createItem(countComments: 0);
    $item->categories()->attach(Category::factory()->create(['name' => '<b>Category</b>']));

    $field = BelongsToMany::make('<b>Categories</b>', 'categories', resource: TestCategoryResource::class)->unescapeLabel();

    match ($mode) {
        'tree' => $field->tree('category_id'),
        'horizontal' => $field->horizontalMode(),
        'inline link' => $field->inLine(link: static fn (): string => '/category'),
        'inline link component' => $field->inLine(link: static fn (mixed $category, string $value): Link => Link::make('/category', $value)),
        'table' => $field,
        'inline' => $field->inLine(', '),
        'inline badge' => $field->inLine(', ', badge: true),
    };

    if ($local !== null) {
        $field->escapeOptionLabels($local);
    }

    $field->fillData($item);

    $html = match ($mode) {
        'tree' => $field->toTreeHtml(),
        'horizontal' => $field->toListHtml(),
        'inline link', 'inline link component', 'inline', 'inline badge' => (string) $field->preview(),
        'table' => (string) $field->getComponent(),
    };

    expect($html)->toContain($escaped ? '&lt;b&gt;Category&lt;/b&gt;' : '<b>Category</b>');
})->with(['tree', 'horizontal', 'inline link', 'inline link component', 'table', 'inline', 'inline badge'])->with([
    'default' => [null, true],
    'local disabled' => [false, false],
    'local enabled' => [true, true],
])->with([true, false]);

it('passes prepared labels to inline badge callbacks', function (bool $escape): void {
    $item = createItem(countComments: 0);
    $item->categories()->attach(Category::factory()->create(['name' => '<img src=x onerror=alert(1)>']));

    $field = BelongsToMany::make('Categories', 'categories', resource: TestCategoryResource::class)
        ->inLine(', ', badge: static fn (mixed $category, string $value): MoonShine\UI\Components\Badge => MoonShine\UI\Components\Badge::make($value, 'red'))
        ->escapeOptionLabels($escape);

    $field->fillData($item);

    expect((string) $field->preview())->toContain($escape ? '&lt;img src=x onerror=alert(1)&gt;' : '<img src=x onerror=alert(1)>');
})->with([true, false]);

it('keeps explicit label preferences of links returned by inline link callbacks', function (bool $escape): void {
    $item = createItem(countComments: 0);
    $item->categories()->attach(Category::factory()->create(['name' => '<b>Category</b>']));

    $field = BelongsToMany::make('Categories', 'categories', resource: TestCategoryResource::class)
        ->inLine(link: static fn (mixed $category, string $value): Link => Link::make('/category', $value)->escapeLabel($escape))
        ->escapeOptionLabels(! $escape);

    $field->fillData($item);

    expect((string) $field->preview())->toContain($escape ? '&lt;b&gt;Category&lt;/b&gt;' : '<b>Category</b>');
})->with([true, false]);

it('passes the option label preference to async search selection', function (?bool $local, string $expected): void {
    $item = createItem(countComments: 0);

    $field = BelongsToMany::make('Categories', 'categories', resource: TestCategoryResource::class)->asyncSearch();

    if ($local !== null) {
        $field->escapeOptionLabels($local);
    }

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([$field]);

    asAdmin()->get($resource->getFormPageUrl($item->getKey()))
        ->assertOk()
        ->assertSee('data-escape-labels="' . $expected . '"', false);
})->with([
    'default' => [null, 'true'],
    'local disabled' => [false, 'false'],
    'local enabled' => [true, 'true'],
]);

it('passes the title preview field to formatted callbacks of the checkbox table', function (): void {
    $item = createItem(countComments: 0);
    $item->categories()->attach(Category::factory()->create(['name' => 'Category']));
    $contexts = [];

    $field = BelongsToMany::make(
        'Categories',
        'categories',
        static function (Category $category, int $index, Preview $ctx) use (&$contexts): string {
            $contexts[] = $ctx->getColumn();

            return 'Formatted ' . $category->name;
        },
        resource: TestCategoryResource::class,
    );
    $field->fillData($item);

    expect((string) $field->getComponent())->toContain('Formatted Category');
    expect($contexts)->not->toBeEmpty()->each->toBe($field->getResourceColumn());
});
