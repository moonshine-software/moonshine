<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Code;
use MoonShine\Fields\Textarea;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Code::make('Code');
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
        ->toBe('moonshine::fields.code');
});

it('methods', function (): void {
    expect($this->field->language('js')->lineNumbers())
        ->language
        ->toBe('js')
        ->lineNumbers
        ->toBeTrue();
});

it('apply', function (): void {
    $data = ['code' => 'this is code'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'code',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->code
        ->toBe($data['code'])
    ;
});
