<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('resources-feature');

class ParentRelationTestItem extends Item
{
    protected $table = 'items';

    public static bool $methodCalled = false;

    public function mutateParent(): bool
    {
        self::$methodCalled = true;

        return true;
    }

    public function legacyCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function otherCategory(): EloquentBelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

class ParentRelationTestResource extends TestItemResource
{
    protected string $model = ParentRelationTestItem::class;

    protected function indexFields(): iterable
    {
        return [
            ...parent::indexFields(),
            BelongsTo::make('Legacy category', 'legacyCategory', resource: TestCategoryResource::class),
        ];
    }
}

beforeEach(function (): void {
    ParentRelationTestItem::$methodCalled = false;
    $this->moonshineCore->resources([ParentRelationTestResource::class]);
    $this->resource = $this->moonshineCore->getResources()->findByClass(ParentRelationTestResource::class);
});

it('does not invoke an unrelated model method from the parent filter', function (): void {
    asAdmin()->getJson($this->resource->getRoute('crud.index') . '?_parentId=mutateParent-1')
        ->assertServerError();

    expect(ParentRelationTestItem::$methodCalled)->toBeFalse();
});

it('does not invoke a static model method from the parent filter', function (): void {
    try {
        asAdmin()->getJson($this->resource->getRoute('crud.index') . '?_parentId=unguard-1')
            ->assertServerError();

        expect(Model::isUnguarded())->toBeFalse();
    } finally {
        Model::reguard();
    }
});

it('filters by a declared parent relation', function (string $relation): void {
    $included = createItem(1, 0);
    $excluded = createItem(1, 0);
    $included->update(['name' => 'Included parent relation item', 'category_id' => Category::factory()->create()->getKey()]);
    $excluded->update(['name' => 'Excluded parent relation item', 'category_id' => Category::factory()->create()->getKey()]);

    asAdmin()->getJson($this->resource->getRoute('crud.index') . '?_parentId=' . $relation . '-' . $included->category_id)
        ->assertOk()
        ->assertSee('Included parent relation item')
        ->assertDontSee('Excluded parent relation item');
})->with(['category', 'otherCategory', 'legacyCategory']);
