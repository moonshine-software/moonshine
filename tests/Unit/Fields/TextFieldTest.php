<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\FormElement;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\InputExtensions\InputExtension;
use MoonShine\UI\InputExtensions\InputEye;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Text::make('Field name');
});

it('field and form element is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function (): void {
    expect($this->field->attributes()->get('type'))
        ->toBe('text');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('mask', function (): void {
    expect($this->field->mask('999'))
        ->getMask()
        ->toBe('999');
});

it('extension', function (): void {
    expect($this->field->extension(new InputEye()))
        ->getExtensions()
        ->toBeCollection()
        ->toHaveCount(1)
        ->getExtensions()
        ->each->toBeInstanceOf(InputExtension::class);
});

it('apply', function (): void {
    $data = ['field_name' => 'test'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'field_name',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->field_name
        ->toBe($data['field_name'])
    ;
});
