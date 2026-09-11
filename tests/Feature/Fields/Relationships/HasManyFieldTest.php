<?php

declare(strict_types=1);

uses()->group('model-relation-fields');
uses()->group('has-many-field');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

it('relatedLink preview', function () {
    createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments', resource: TestCommentResource::class)->relatedLink(),
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('<span class="badge">6</span>', false)
    ;
});

it('relatedLink preview empty', function () {
    createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments', resource: TestCommentResource::class)
            ->relatedLink(),
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
    ;
});

it('relatedLink value', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments', resource: TestCommentResource::class)
            ->relatedLink(),
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertSee('<span class="badge">16</span>', false)
        ->assertOk()
    ;
});

it('relatedLink value empty', function () {
    $item = createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments', resource: TestCommentResource::class)
            ->relatedLink(),
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
    ;
});

it('relatedLink preview condition', function () {
    $item = createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)
            ->relatedLink(condition: static function (int $count): bool {
                return $count > 10;
            })
        ,
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments->first()->content)
        ->assertDontSee('<span class="badge">6</span>', false)
    ;
});

it('relatedLink value condition', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)
            ->relatedLink(condition: static function (int $count, HasMany $field): bool {
                return $field->toRelatedCollection()->count() > 20;
            })
        ,
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[15]->content)
        ->assertDontSee('<span class="badge">16</span>', false)
    ;
});

it('without modals', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)
            ->withoutModals()
            ->creatable()
        ,
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[15]->content)
        ->assertDontSee('<span class="badge">16</span>', false)
    ;
});

it('stop getting id from url', function () {
    $item = createItem(countComments: 1);
    //$comment = $item->comments->first();
    $hasMany = HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)->creatable();

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        $hasMany,
    ]);

    fakeRequest(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id])
    );

    expect($hasMany->getResource()->getItemID())
        ->toBeNull();
});

it('modify builder', function () {
    $item = createItem(countComments: 2);

    $comments = Comment::query()->get();

    $commentFirst = $comments->first();
    $commentLast = $comments->last();

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Name'),
        HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)
            ->modifyBuilder(
                fn (Relation $relation) => $relation->where('id', $commentFirst->id)
            )
        ,
    ]);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($commentFirst->content)
        ->assertDontSee($commentLast->content)
    ;

    asAdmin()
        ->get($this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($commentFirst->content)
        ->assertDontSee($commentLast->content)
    ;
});

it('applies a default query tag with an eloquent builder to has many items', function (string $page, bool $async): void {
    $this->withoutExceptionHandling();

    $item = createItem(countComments: 2);
    $otherItem = createItem(countComments: 1);

    $item->comments[0]->update(['content' => 'Visible related comment']);
    $item->comments[1]->update(['content' => 'Filtered related comment']);
    $otherItem->comments[0]->update(['content' => 'Visible unrelated comment']);

    $commentResource = app(TestResource::class)
        ->setTestModel(Comment::class)
        ->setTestUriKey('query-tag-comments')
        ->setTestFields([
            ID::make(),
            Text::make('Content'),
        ])
        ->setTestQueryTags([
            QueryTag::make(
                'Visible comments',
                static fn (Builder $query): Builder => $query->where('content', 'like', 'Visible%')
            )->default(static fn (): bool => true),
        ]);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        HasMany::make('Comments', 'comments', resource: $commentResource),
    ]);

    $url = $async
        ? $this->moonshineCore->getRouter()->to('has-many.list', [
            'pageUri' => $resource->getPages()->findByClass($page)->getUriKey(),
            'resourceUri' => $resource->getUriKey(),
            'resourceItem' => $item->getKey(),
            '_relation' => 'comments',
        ])
        : $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: $page,
            resource: $resource,
            params: ['resourceItem' => $item->getKey()]
        );

    asAdmin()->get($url)
        ->assertOk()
        ->assertSee('Visible related comment')
        ->assertDontSee('Filtered related comment')
        ->assertDontSee('Visible unrelated comment');
})->with([
    'detail page' => DetailPage::class,
    'form page' => FormPage::class,
])->with([
    'initial render' => false,
    'async list' => true,
]);
