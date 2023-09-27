<?php

declare(strict_types=1);

uses()->group('model-relation-fields');
uses()->group('has-many-field');

use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

it('onlyLink preview', function () {
    createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page($resource, 'index-page'))
        ->assertOk()
        ->assertSee('(6)')
    ;
});

it('onlyLink preview empty', function () {
    createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page($resource, 'index-page'))
        ->assertOk()
    ;
});

it('onlyLink value', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page($resource, 'form-page', ['resourceItem' => $item->id]))
        ->assertSee('(16)')
        ->assertOk()
    ;
});

it('onlyLink value empty', function () {
    $item = createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page($resource, 'form-page', ['resourceItem' => $item->id]))
        ->assertOk()
    ;
});

it('onlyLink preview condition', function () {
    $item = createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->onlyLink(condition: function (int $count): bool {
                return $count > 10;
            })
        ,
    ]);

    asAdmin()
        ->get(to_page($resource, 'index-page'))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[0]->content)
        ->assertDontSee('(6)')
    ;
});

it('onlyLink value condition', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->onlyLink(condition: function (int $count, Field $field): bool {
                return $field->toValue()->total() > 20;
            })
        ,
    ]);

    asAdmin()
        ->get(to_page($resource, 'form-page', ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[15]->content)
        ->assertDontSee('(16)')
    ;
});
