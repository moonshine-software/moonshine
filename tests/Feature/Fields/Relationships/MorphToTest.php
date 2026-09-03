<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Fields\ID;

uses()->group('model-relation-fields');
uses()->group('morph-field');

beforeEach(function (): void {
    $this->resource = app(TestImageResource::class);
    $this->items = Item::factory(10)->create();
    $this->item = Item::factory()->createOne();
    $this->image = ImageModel::create([
        'imageable_id' => $this->item->getKey(),
        'imageable_type' => Item::class,
    ]);
});

it('show field on pages', function () {
    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: DetailPage::class, resource: $this->resource, params: ['resourceItem' => $this->image->getKey()])
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;


    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => $this->image->getKey()])
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
        ->assertSee("x-data=\"{morphType: 'MoonShine\\\\Tests\\\\Fixtures\\\\Models\\\\Item'}\"", false)
    ;
});

it('apply as base', function () {
    saveImageable($this->resource, $this->image);

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('Imageable')
        ->assertSee($this->image->imageable->name)
    ;
});

it('renders morph type as iterable field inside relation repeater', function () {
    $imageResource = repeaterImageResource();

    $resource = addFieldsToTestResource(
        RelationRepeater::make('Images', 'images', resource: $imageResource)
            ->fields([
                ID::make(),
                MorphTo::make('Imageable', 'imageable', resource: $imageResource)
                    ->types([
                        Item::class => 'name',
                        Category::class => 'name',
                    ]),
            ])
    );

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: FormPage::class,
            resource: $resource,
            params: ['resourceItem' => $this->item->getKey()]
        )
    )
        ->assertOk()
        ->assertSee('data-name="images[${index0}][imageable_type]"', false);
});

it('renders nested morph to relation repeater fields when lazy loading is disabled', function () {
    ImageModel::create([
        'imageable_id' => $this->item->getKey(),
        'imageable_type' => Item::class,
    ]);

    $imageResource = repeaterImageResource();

    $resource = addFieldsToTestResource(
        RelationRepeater::make('Images', 'images', resource: $imageResource)
            ->fields([
                ID::make(),
                MorphTo::make('Imageable', 'imageable', resource: $imageResource)
                    ->types([
                        Item::class => 'name',
                        Category::class => 'name',
                    ]),
            ])
    );

    Model::preventLazyLoading();

    try {
        asAdmin()->get(
            $this->moonshineCore->getRouter()->getEndpoints()->toPage(
                page: FormPage::class,
                resource: $resource,
                params: ['resourceItem' => $this->item->getKey()]
            )
        )
            ->assertOk()
            ->assertSee('data-name="images[${index0}][imageable_type]"', false);
    } finally {
        Model::preventLazyLoading(false);
    }
});

it('applies morph to inside relation repeater', function () {
    $category = Category::factory()->create();
    $imageResource = repeaterImageResource();

    $resource = addFieldsToTestResource(
        RelationRepeater::make('Images', 'images', resource: $imageResource)
            ->fields([
                ID::make(),
                MorphTo::make('Imageable', 'imageable', resource: $imageResource)
                    ->types([
                        Item::class => 'name',
                        Category::class => 'name',
                    ]),
            ])
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'images' => [
                1 => [
                    'id' => $this->image->getKey(),
                    'imageable_type' => Category::class,
                    'imageable_id' => $category->getKey(),
                ],
            ],
        ]
    )->assertRedirect();

    $this->image->refresh();

    expect($this->image)
        ->imageable_type->toBe(Category::class)
        ->imageable_id->toBe($category->getKey());
});

it('updates morph to inside has many relation repeater', function () {
    $category = Category::factory()->create();
    $target = Item::factory()->create();
    $comment = Comment::factory()
        ->for($this->item)
        ->create([
            'imageable_type' => Category::class,
            'imageable_id' => $category->getKey(),
        ]);

    $commentResource = app(TestCommentResource::class);

    $resource = addFieldsToTestResource(
        RelationRepeater::make('Comments', 'comments', resource: $commentResource)
            ->fields([
                ID::make(),
                MorphTo::make('Imageable', 'imageable', resource: $commentResource)
                    ->types([
                        Item::class => 'name',
                        Category::class => 'name',
                    ]),
            ])
    );

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        [
            'comments' => [
                1 => [
                    'id' => $comment->getKey(),
                    'imageable_type' => Item::class,
                    'imageable_id' => $target->getKey(),
                ],
            ],
        ]
    )->assertRedirect();

    $comment->refresh();

    expect($comment)
        ->imageable_type->toBe(Item::class)
        ->imageable_id->toBe($target->getKey());
});

it('export', function (): void {
    morphToExport($this->image, randomImageableId());
});

it('import', function (): void {

    $id = randomImageableId();

    $file = morphToExport($this->image, $id);

    $import = ImportHandler::make('');

    asAdmin()->post(
        $this->resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->image->refresh();

    expect($this->image->imageable_id)
        ->toBe($id)
    ;
});

function morphToExport(ImageModel $item, int $newId): ?string
{
    $resource = app(TestImageResource::class);
    $item->imageable_id = $newId;
    $item->imageable_type = Item::class;

    $item->save();

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-image-resource.csv');

    expect($file)
        ->toContain('Imageable')
        ->toContain($item->imageable->id)
    ;

    return $file;
}

function saveImageable(ModelResource $resource, Model $item): void
{
    $id = randomImageableId();
    $data = ['imageable_id' => $id, 'imageable_type' => Item::class];

    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    $resource->getIndexFields()->each(static function ($field) {
        $field->reset();
    });

    expect($item->imageable_id)
        ->toBe($id)
    ;
}

function randomImageableId(): int
{
    return Item::query()->inRandomOrder()->first()->id;
}

function repeaterImageResource(): TestResource
{
    $resource = clone app(TestResource::class);
    $resource
        ->setTestModel(ImageModel::class)
        ->setTestUriKey('test-image-repeater-resource');

    return $resource->setTestFields([
        ID::make(),
        MorphTo::make('Imageable', 'imageable', resource: $resource)
            ->types([
                Item::class => 'name',
                Category::class => 'name',
            ]),
    ]);
}
