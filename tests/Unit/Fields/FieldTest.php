<?php

use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function () {
    $this->field = Text::make('Field name');
});

it('show or hide / index', function () {
    expect($this->field)
        ->isOnIndex()
        ->toBeTrue()
        ->hideOnIndex()
        ->isOnIndex()
        ->toBeFalse()
        ->showOnIndex(fn () => true)
        ->isOnIndex()
        ->toBeTrue();
});

it('show or hide / form', function () {
    expect($this->field)
        ->isOnForm()
        ->toBeTrue()
        ->hideOnForm()
        ->isOnForm()
        ->toBeFalse()
        ->showOnForm(fn () => true)
        ->isOnForm()
        ->toBeTrue();
});

it('show or hide / detail', function () {
    expect($this->field)
        ->isOnDetail()
        ->toBeTrue()
        ->hideOnDetail()
        ->isOnDetail()
        ->toBeFalse()
        ->showOnDetail(fn () => true)
        ->isOnDetail()
        ->toBeTrue();
});

it('show or hide / export', function () {
    expect($this->field->showOnExport())
        ->isOnExport()
        ->toBeTrue()
        ->hideOnExport()
        ->isOnExport()
        ->toBeFalse()
        ->showOnExport(fn () => true)
        ->isOnExport()
        ->toBeTrue();
});

it('show or hide / import', function () {
    expect($this->field->useOnImport())
        ->isOnImport()
        ->toBeTrue()
        ->useOnImport(false)
        ->isOnImport()
        ->toBeFalse()
        ->useOnImport(fn () => true)
        ->isOnImport()
        ->toBeTrue();
});

it('show or hide / now form', function () {
    $resource = TestResourceBuilder::new(addRoutes: true);

    expect($this->field)
        ->isNowOnForm()
        ->toBeFalse();

    $this->get($resource->route('create'));

    expect($this->field)
        ->isNowOnForm()
        ->toBeTrue();
});

it('correct link', function () {
    $link = fake()->url();

    expect($this->field->addLink('Link', $link, true))
        ->getLinkName()
        ->toBe('Link')
        ->getLinkValue()
        ->toBe($link)
        ->isLinkBlank()
        ->toBeTrue();
});

it('sortable', function () {
    fakeRequest('/');

    expect($this->field)
        ->isSortable()
        ->toBeFalse()
        ->and($this->field->sortable())
        ->isSortable()
        ->toBeTrue()
        ->sortQuery()
        ->toBe(
            request()->fullUrlWithQuery([
                'order' => [
                    'field' => 'field_name',
                    'type' => 'asc',
                ],
            ])
        );

    fakeRequest('/', 'GET', ['order' => ['field' => 'field_name', 'type' => 'asc']]);

    expect($this->field)
        ->sortQuery()
        ->toBe(
            request()->fullUrlWithQuery([
                'order' => [
                    'field' => 'field_name',
                    'type' => 'desc',
                ],
            ])
        )
        ->sortType('asc')
        ->toBeTrue()
        ->sortActive()
        ->toBeTrue();
});

it('form view value', function () {
    $this->field->setField('email');

    $item = MoonshineUser::factory()->create();

    expect($this->field->formViewValue($item))
        ->toBe($item->email);

    $valueCallbackField = Text::make('Email', 'email', function (MoonshineUser $item) {
        return $item->id.'|'.$item->email;
    });

    expect($valueCallbackField->formViewValue($item))
        ->toBe($item->id.'|'.$item->email);

    $this->field->default('-');

    expect($this->field->formViewValue($item->newInstance()))
        ->toBe('-');
});

it('index/export view value', function () {
    $this->field->setField('email');

    $item = MoonshineUser::factory()->create();

    expect($this->field->indexViewValue($item))->and($this->field->exportViewValue($item))
        ->toBe($item->email);

    $valueCallbackField = Text::make('Email', 'email', function (MoonshineUser $item) {
        return $item->id.'|'.$item->email;
    });

    expect($valueCallbackField->indexViewValue($item))->and($valueCallbackField->exportViewValue($item))
        ->toBe($item->id.'|'.$item->email);
});

it('can save', function () {
    expect($this->field)
        ->isCanSave()
        ->toBeTrue()
        ->and($this->field->canSave(false))
        ->isCanSave()
        ->toBeFalse();
});

it('save', function () {
    $this->field->setField('email');

    fakeRequest('/', 'POST', [$this->field->field() => 'testing']);
    expect($this->field)
        ->save(MoonshineUser::factory()->create())
        ->email
        ->toBe('testing');
});
