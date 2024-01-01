<?php

use MoonShine\Fields\ID;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('resources-feature');
uses()->group('resources-routes');

beforeEach(function () {
    $this->item = createItem(1, 1);
    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make(),
            StackFields::make('')->fields([
                Text::make('hideOnCreate')->hideOnCreate(),
                Text::make('hideOnForm')->hideOnForm(),
                Text::make('hideOnDetail')->hideOnDetail(),
                Text::make('hideOnIndex')->hideOnIndex(),
            ]),
        ]);
});

it('is now on index', function () {
    asAdmin()->get($this->resource->indexPageUrl())->assertOk();

    expect($this->resource)
        ->isNowOnIndex()
        ->toBeTrue()
        ->isNowOnDetail()
        ->toBeFalse()
        ->isNowOnForm()
        ->toBeFalse()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->getFields()->onlyFields()->indexFields()
        ->toHaveCount(4)
    ;
});

it('is now on detail', function () {
    asAdmin()->get($this->resource->detailPageUrl($this->item))->assertOk();

    expect($this->resource)
        ->isNowOnDetail()
        ->toBeTrue()
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnForm()
        ->toBeFalse()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->getFields()->onlyFields()->detailFields()
        ->toHaveCount(4)
    ;
});

it('is now on update form', function () {
    asAdmin()->get($this->resource->formPageUrl($this->item))->assertOk();

    expect($this->resource)
        ->isNowOnForm()
        ->toBeTrue()
        ->isNowOnCreateForm()
        ->toBeFalse()
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnDetail()
        ->toBeFalse()
        ->getFields()->onlyFields()->formFields()
        ->toHaveCount(3)
    ;
});

it('is now on create form', function () {
    asAdmin()->get($this->resource->formPageUrl())->assertOk();

    expect($this->resource)
        ->isNowOnCreateForm()
        ->toBeTrue()
        ->isNowOnForm()
        ->toBeTrue()
        ->isNowOnIndex()
        ->toBeFalse()
        ->isNowOnDetail()
        ->toBeFalse()
        ->getFields()->onlyFields()->formFields()
        ->toHaveCount(3)
    ;
});
