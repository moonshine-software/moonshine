<?php

use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Text::make('Field name');
});

it('show or hide / index', function (): void {
    expect($this->field)
        ->isOnIndex()
        ->toBeTrue()
        ->hideOnIndex()
        ->isOnIndex()
        ->toBeFalse()
        ->showOnIndex(fn (): bool => true)
        ->isOnIndex()
        ->toBeTrue();
});

it('show or hide / form', function (): void {
    expect($this->field)
        ->isOnForm()
        ->toBeTrue()
        ->hideOnForm()
        ->isOnForm()
        ->toBeFalse()
        ->showOnForm(fn (): bool => true)
        ->isOnForm()
        ->toBeTrue();
});

it('show or hide / detail', function (): void {
    expect($this->field)
        ->isOnDetail()
        ->toBeTrue()
        ->hideOnDetail()
        ->isOnDetail()
        ->toBeFalse()
        ->showOnDetail(fn (): bool => true)
        ->isOnDetail()
        ->toBeTrue();
});

it('show or hide / export', function (): void {
    expect($this->field->showOnExport())
        ->isOnExport()
        ->toBeTrue()
        ->hideOnExport()
        ->isOnExport()
        ->toBeFalse()
        ->showOnExport(fn (): bool => true)
        ->isOnExport()
        ->toBeTrue();
});

it('show or hide / import', function (): void {
    expect($this->field->useOnImport())
        ->isOnImport()
        ->toBeTrue()
        ->useOnImport(false)
        ->isOnImport()
        ->toBeFalse()
        ->useOnImport(fn (): bool => true)
        ->isOnImport()
        ->toBeTrue();
});

it('show or hide / now form', function (): void {
    $resource = TestResourceBuilder::new(addRoutes: true);

    expect($this->field)
        ->isNowOnForm()
        ->toBeFalse();

    $this->get($resource->route('create'));

    expect($this->field)
        ->isNowOnForm()
        ->toBeTrue();
});

it('correct link', function (): void {
    $link = fake()->url();

    expect($this->field->addLink('Link', $link, true))
        ->getLinkName()
        ->toBe('Link')
        ->getLinkValue()
        ->toBe($link)
        ->isLinkBlank()
        ->toBeTrue();
});

it('sortable', function (): void {
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

it('form view value', function (): void {
    $this->field->setField('email');

    $item = MoonshineUser::factory()->create();

    expect($this->field->formViewValue($item))
        ->toBe($item->email);

    $valueCallbackField = Text::make('Email', 'email', fn (MoonshineUser $item): string => $item->id . '|' . $item->email);

    expect($valueCallbackField->formViewValue($item))
        ->toBe($item->id . '|' . $item->email);

    $this->field->default('-');

    expect($this->field->formViewValue($item->newInstance()))
        ->toBe('-');
});

it('index/export view value', function (): void {
    $this->field->setField('email');

    $item = MoonshineUser::factory()->create();

    expect($this->field->indexViewValue($item))->and($this->field->exportViewValue($item))
        ->toBe($item->email);

    $valueCallbackField = Text::make('Email', 'email', fn (MoonshineUser $item): string => $item->id . '|' . $item->email);

    expect($valueCallbackField->indexViewValue($item))->and($valueCallbackField->exportViewValue($item))
        ->toBe($item->id . '|' . $item->email);
});

it('can save', function (): void {
    expect($this->field)
        ->isCanSave()
        ->toBeTrue()
        ->and($this->field->canSave(false))
        ->isCanSave()
        ->toBeFalse();
});

it('save', function (): void {
    $this->field->setField('email');

    fakeRequest('/', 'POST', [$this->field->field() => 'testing']);
    expect($this->field)
        ->save(MoonshineUser::factory()->create())
        ->email
        ->toBe('testing');
});
