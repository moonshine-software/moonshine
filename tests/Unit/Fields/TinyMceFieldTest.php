<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Textarea;
use MoonShine\Fields\TinyMce;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = TinyMce::make('TinyMce');
});

it('textarea is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.tinymce');
});

it('has assets', function (): void {
    expect($this->field->getAssets())
        ->toBeArray()
        ->not->toBeEmpty();
});

it('apply', function (): void {
    $data = ['tinymce' => 'test'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'tinymce',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->tinymce
        ->toBe($data['tinymce'])
    ;
});
