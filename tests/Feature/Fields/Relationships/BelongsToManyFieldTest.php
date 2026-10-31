<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\CustomKeyCategory;
use MoonShine\Tests\Fixtures\Models\CustomPivotItem;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Text;

use function Pest\Laravel\get;

uses()->group('model-relation-fields');
uses()->group('belongs-to-many-field');

mutates(BelongsToMany::class);

beforeEach(function () {
    $this->item = createItem(countComments: 0);

    $this->pivotFields = [
        Text::make('Pivot 1', 'pivot_1'),
        Text::make('Pivot 2', 'pivot_2'),
        File::make('Pivot 3', 'pivot_3'),
    ];

    $this->field = BelongsToMany::make('Categories', resource: TestCategoryResource::class)
        ->fields($this->pivotFields)
        ->fillData($this->item)
    ;

    expect($this->item->data)
        ->toBeEmpty();
});

function testBelongsToManyValue(TestResource $resource, Item $item, array $data, ?array $pivotData = null)
{
    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        [
            'categories' => $data,
            'categories_pivot' => $pivotData,
        ]
    )->assertRedirect();

    $item->refresh();

    expect($item->categories->modelKeys())->toEqualCanonicalizing(array_values($data));
}

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data, []);
});

it('apply as base with pivot', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2']];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(static function ($category) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2');
    });

    // unsync
    $data = $categories->random(1)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);
});

it('clears an empty selection without changing another items relations', function (bool $selectMode): void {
    $field = $selectMode ? $this->field->selectMode() : $this->field;
    $resource = addFieldsToTestResource($field);
    $category = Category::factory()->create();
    $otherItem = createItem(countComments: 0);

    $this->item->categories()->attach($category->getKey(), ['pivot_1' => 'remove']);
    $otherItem->categories()->attach($category->getKey(), ['pivot_1' => 'keep']);

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'categories' => [],
        'categories_pivot' => [],
    ])->assertRedirect();

    expect($this->item->categories()->count())->toBe(0)
        ->and($otherItem->categories()->sole()->pivot->pivot_1)->toBe('keep');
})->with([
    'select' => true,
    'pivot' => false,
]);


it('apply as base with pivot and file', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) use ($file) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBe($file->hashName())
        ;
    });
});

it('apply as base with pivot and file after remove', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2']];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBeNull()
        ;
    });
});

it('apply as base with pivot and file stay by hidden', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'hidden_pivot_3' => $file->hashName()]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) use ($file) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBe($file->hashName())
        ;
    });
});

it('apply as tree', function () {
    $resource = addFieldsToTestResource(
        $this->field->tree('category_id')
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id')->all();

    testBelongsToManyValue($resource, $this->item, $data);
});

it('apply as select', function () {
    $resource = addFieldsToTestResource(
        $this->field->selectMode()
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id')->all();

    testBelongsToManyValue($resource, $this->item, $data);
});

it('apply as filter', function (): void {
    $field = $this->field
        ->wrapName('filter');

    $query = Item::query();

    get('/?filter[categories][3][_checked]=1');

    $field
        ->onApply((new BelongsToManyModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        );

    $sql = strtolower($query->toRawSql());

    expect($sql)
        ->toContain('category_item')
        ->toContain('category_id')
        ->toContain('in (3)');
});

function belongsToManyExport(Item $item, BelongsToMany $field): ?string
{
    $category = Category::factory()->create();

    $item->categories()->attach($category);
    $item->refresh();

    $resource = addFieldsToTestResource(
        $field,
        'exportFields'
    );

    $export = ExportHandler::make('');

    Storage::disk('public')->delete('test-resource.csv');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Categories');

    return $file;
}

it('export', function (): void {
    belongsToManyExport($this->item, $this->field);
});

it('import', function (): void {

    $file = belongsToManyExport($this->item, $this->field);

    $resource = addFieldsToTestResource(
        $this->field,
        'importFields'
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->categories->count())
        ->toBe(1)
    ;
});

it('uses relation defaults before a parent model is bound', function (): void {
    $field = BelongsToMany::make('Categories', resource: TestCategoryResource::class);

    expect($field->getPivotAs())->toBe('pivot')
        ->and($field->getRelatedKeyName())->toBe('id');
});

it('exports related keys from both models and raw filter values', function (bool $models): void {
    $category = Category::factory()->create();
    $this->field->setValue(new EloquentCollection([$models ? $category : $category->getKey()]));

    expect(json_decode($this->field->toRawValue(), true))->toBe([$category->getKey()])
        ->and($this->field->getSelectedValue())->toBe([$category->getKey()]);
})->with(['models' => true, 'raw filter ids' => false]);

it('uses the related models custom key for selected options', function (): void {
    $parent = CustomPivotItem::query()->findOrFail($this->item->getKey());
    $category = new CustomKeyCategory(['name' => 'Custom key', 'category_id' => 421]);
    $field = BelongsToMany::make('Categories', 'keyedCategories', resource: TestCategoryResource::class)
        ->fillData($parent)
        ->setValue(new EloquentCollection([$category]));

    expect($field->getSelectedValue())->toBe([421]);
});

it('saves pivot values through a custom pivot accessor', function (): void {
    $parent = CustomPivotItem::query()->findOrFail($this->item->getKey());
    $field = BelongsToMany::make('Categories', resource: TestCategoryResource::class)
        ->fields([Text::make('Pivot 1', 'pivot_1')])
        ->fillData($parent);
    $resource = \MoonShine\Tests\Fixtures\Resources\TestResourceBuilder::new(CustomPivotItem::class)->setTestFields([$field]);
    $category = Category::factory()->create();

    asAdmin()->put($resource->getRoute('crud.update', $parent->getKey()), [
        'categories' => [$category->getKey() => $category->getKey()],
        'categories_pivot' => [$category->getKey() => ['membership' => ['pivot_1' => 'custom value']]],
    ])->assertRedirect();

    expect($parent->categories()->sole()->membership->pivot_1)->toBe('custom value');
});

it('runs nested before apply hooks outside pivot modal mode', function (bool $modal): void {
    $this->field->pivotModalMode($modal)->fields([
        Text::make('Pivot 1', 'pivot_1')->onBeforeApply(static function (Item $item): Item {
            $item->content = 'Prepared by nested field';

            return $item;
        }),
    ]);

    $original = $this->item->content;
    $this->field->beforeApply($this->item);

    expect($this->item->content)->toBe($modal ? $original : 'Prepared by nested field');
})->with(['standard' => false, 'pivot modal' => true]);

it('prepares simple relation selections without pivot side effects', function (string $mode): void {
    $field = match ($mode) {
        'select' => $this->field->selectMode(),
        'tree' => $this->field->tree('category_id'),
        'horizontal' => $this->field->horizontalMode(),
    };
    fakeRequest(parameters: ['categories' => [12, 27]]);

    Field::silentApply(fn () => $field->afterApply($this->item));

    expect($this->item->getAttribute('categories')->all())->toBe(['12', '27'])
        ->and($this->item->categories()->exists())->toBeFalse();
})->with(['select', 'tree', 'horizontal']);

it('keeps repeated selections and their distinct pivot values when deduplication is disabled', function (): void {
    $category = Category::factory()->create();
    $oldCategory = Category::factory()->create();
    $this->item->categories()->attach($oldCategory->getKey(), ['pivot_1' => 'remove old selection']);
    $resource = addFieldsToTestResource($this->field->deduplication(false));

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'categories' => [$category->getKey(), $category->getKey()],
        'categories_pivot' => [
            ['pivot' => ['pivot_1' => 'first']],
            ['pivot' => ['pivot_1' => 'second']],
        ],
    ])->assertRedirect();

    expect($this->item->categories()->get()->pluck('pivot.pivot_1')->all())->toBe(['first', 'second']);
});

it('synchronizes simple selections without applying unused pivot fields', function (string $mode): void {
    $field = match ($mode) {
        'select' => $this->field->selectMode(),
        'tree' => $this->field->tree('category_id'),
        'horizontal' => $this->field->horizontalMode(),
    };
    $field->fields([
        Text::make('Pivot 1', 'pivot_1')->onApply(static fn () => throw new LogicException('Unused pivot field was applied')),
    ]);
    $resource = addFieldsToTestResource($field);
    $category = Category::factory()->create();
    $this->item->categories()->attach($category->getKey(), ['pivot_1' => 'keep existing']);

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'categories' => [$category->getKey()],
    ])->assertRedirect();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBe('keep existing');
})->with(['select', 'tree', 'horizontal']);

it('stores absent pivot values as null', function (): void {
    $resource = addFieldsToTestResource($this->field->fields([Text::make('Pivot 1', 'pivot_1')]));
    $category = Category::factory()->create();

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'categories' => [$category->getKey() => $category->getKey()],
    ])->assertRedirect();

    expect($this->item->categories()->sole()->pivot->pivot_1)->toBeNull();
});

it('previews related models supplied as a plain array', function (): void {
    $category = Category::factory()->create(['name' => 'Plain array category']);
    $this->item->setRelation('categories', [$category]);
    $field = BelongsToMany::make('Categories', resource: TestCategoryResource::class)
        ->inLine()
        ->fillData($this->item);

    expect((string) $field->preview())->toBe('Plain array category');
});

it('initializes selected values from a raw array of related ids', function (): void {
    $category = Category::factory()->create();
    $field = $this->field->selectMode()->setValue([$category->getKey()]);

    expect($field->getSelectedValue())->toBe([$category->getKey()])
        ->and($field->getKeys())->toBe([$category->getKey()]);
});

it('restores record keys on table rows when deduplication is enabled again', function (): void {
    $category = Category::factory()->create();
    $field = $this->field->fields([])->deduplication(false)->deduplication()
        ->valuesQuery(static fn ($query) => $query->whereKey($category->getKey()));

    expect($field->getComponent()->getRows()->sole()->getKey())->toBe($category->getKey());
});

it('passes the editable table context to a table modifier', function (): void {
    $field = $this->field->columnLabel('Selected categories')
        ->modifyTable(static fn ($table, bool $preview) => $table->customAttributes([
            'data-mode' => $preview ? 'preview' : 'form',
        ]));
    $table = $field->getComponent();

    expect($table->getAttributes()->get('data-mode'))->toBe('form')
        ->and($table->getName())->toBe('belongs_to_many_categories')
        ->and($table->getFields()->onlyFields()->extractLabels())->toContain('Selected categories');
});

it('loads only selected models for async relation tables', function (bool $async): void {
    $selected = Category::factory()->create();
    $other = Category::factory()->create();
    $field = $this->field->setValue(new EloquentCollection([$selected->getKey() => $selected->getKey()]));

    if ($async) {
        $field->asyncSearch();
    }

    $keys = $field->getAvailableValues()->modelKeys();

    expect($keys)->toContain($selected->getKey());

    if ($async) {
        expect($keys)->toBe([$selected->getKey()]);
    } else {
        expect($keys)->toContain($other->getKey());
    }
})->with(['async' => true, 'standard' => false]);

it('creates an empty template row only for an empty async relation table', function (bool $async): void {
    $field = $this->field->valuesQuery(static fn ($query) => $query->whereRaw('1 = 0'));

    if ($async) {
        $field->asyncSearch();
    }

    expect($field->getComponent()->getRows())->toHaveCount($async ? 1 : 0);
})->with(['async' => true, 'standard' => false]);
