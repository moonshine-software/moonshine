<?php

use Illuminate\View\ComponentAttributeBag;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function () {
    $this->field = Text::make('Field name');
});

it('make instance', function () {
    expect($this->field)
        ->label()
        ->toBe('Field name')
        ->field()
        ->toBe('field_name')
        ->relation()
        ->toBeNull()
        ->resource()
        ->toBeNull()
        ->name()
        ->toBe('field_name')
        ->name()
        ->toBe('field_name')
        ->id()
        ->toBe('field_name');
});

it('custom field', function () {
    $field = Text::make('Field name', 'custom_field');

    expect($field)
        ->getView()
        ->toBe('moonshine::fields.input')
        ->label()
        ->toBe('Field name')
        ->field()
        ->toBe('custom_field')
        ->name()
        ->toBe('custom_field')
        ->name()
        ->toBe('custom_field')
        ->id()
        ->toBe('custom_field')
        ->type()
        ->toBe('text');
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

it('nullable', function () {
    expect($this->field)
        ->isNullable()
        ->toBeFalse()
        ->and($this->field->nullable())
        ->isNullable()
        ->toBeTrue();
});

it('is group', function () {
    expect($this->field)
        ->isGroup()
        ->toBeFalse();
});

it('has parent', function () {
    expect($this->field)
        ->hasParent()
        ->toBeFalse();
});

it('not relationship field', function () {
    expect($this->field)
        ->hasRelationship()
        ->toBeFalse()
        ->canBeResourceMode()
        ->toBeFalse()
        ->isResourceModeField()
        ->toBeFalse()
        ->hasRelatedValues()
        ->toBeFalse()
        ->belongToOne()
        ->toBeFalse();
});

it('field container', function () {
    expect($this->field)
        ->hasFieldContainer()
        ->toBeTrue()
        ->and($this->field->fieldContainer(false))
        ->hasFieldContainer()
        ->toBeFalse();
});

it('request value', function () {
    fakeRequest('/', 'POST', [$this->field->field() => 'testing']);

    expect($this->field)
        ->requestValue()
        ->toBe('testing');

    fakeRequest('/');

    expect($this->field)
        ->requestValue()
        ->toBeFalse()
        ->and($this->field->default('-'))
        ->requestValue()
        ->toBe('-');
});

it('hint', function () {
    expect($this->field->hint('Hint'))
        ->getHint()
        ->toBe('Hint');
});

it('html attributes', function () {
    expect($this->field->setAttribute('data-test', true))
        ->attributes()
        ->toBeInstanceOf(ComponentAttributeBag::class)
        ->getAttribute('data-test')
        ->toBeTrue()
        ->and($this->field->readonly())
        ->isReadonly()
        ->toBeTrue()
        ->and($this->field->disabled())
        ->isDisabled()
        ->toBeTrue()
        ->and($this->field->required())
        ->isRequired()
        ->toBeTrue();
});

it('custom attributes', function () {
    expect($this->field->customAttributes(['readonly' => true, 'multiple' => true]))
        ->getAttribute('readonly')
        ->toBeTrue()
        ->getAttribute('multiple')
        ->toBeTrue();
});

it('custom class', function () {
    expect($this->field->customClasses(['class_1', 'class_2']))
        ->getAttribute('class')
        ->toBe('class_1 class_2');
});

it('xModel', function () {
    expect($this->field->xModel())
        ->xModelField()
        ->toBe('item.field_name')
        ->getAttribute('x-model-field')
        ->toBe('item.field_name')
        ->getAttribute('x-model-has-fields')
        ->toBeFalse()
        ->getAttribute('x-model.lazy')
        ->toBe('item.field_name')
        ->getAttribute('x-bind:name')
        ->toBe('`field_name`')
        ->getAttribute('x-bind:id')
        ->toBe('`field_name`')
        ->clearXModel()
        ->getAttribute('x-model-field')
        ->toBeNull();
});

it('show when', function () {
    expect($this->field)
        ->hasShowWhen()
        ->toBeFalse()
        ->and($this->field->showWhen('title', 'testing'))
        ->hasShowWhen()
        ->toBeTrue()
        ->showWhenField->toBe('title')
        ->showWhenValue->toBe('testing');
});

it('assets', function () {
    expect($this->field)
        ->getAssets()
        ->toBeArray()
        ->toBeEmpty();
});
