<?php

declare(strict_types=1);

uses()->group('model-relation-fields');
uses()->group('has-many-field');

use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

it('onlyLink preview', function () {
    createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->countLinkMode(),
    ]);

    asAdmin()
        ->get(toPage(page: IndexPage::class, resource: $resource))
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
            ->countLinkMode(),
    ]);

    asAdmin()
        ->get(toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
    ;
});

it('onlyLink value', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->countLinkMode(),
    ]);

    asAdmin()
        ->get(toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
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
            ->countLinkMode(),
    ]);

    asAdmin()
        ->get(toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
    ;
});

it('onlyLink preview condition', function () {
    $item = createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->countLinkMode(condition: function (int $count): bool {
                return $count > 10;
            })
        ,
    ]);

    asAdmin()
        ->get(toPage(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments->first()->content)
        ->assertDontSee('(6)')
    ;
});

it('onlyLink value condition', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->countLinkMode(condition: function (int $count, Field $field): bool {
                return $field->toValue()->total() > 20;
            })
        ,
    ]);

    asAdmin()
        ->get(toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[15]->content)
        ->assertDontSee('(16)')
    ;
});
