<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\TinyMce;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = TinyMce::make('TinyMce');
});

it('textarea is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Textarea::class);
});

it('type', function (): void {
    expect($this->field->getAttributes()->get('type'))
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

it('tinymce methods', function (): void {
    expect($this->field)
        ->commentAuthor('test')
        ->commentAuthor->toBe('test')
        ->menubar('test')
        ->menubar->toBe('test')
        ->addConfig('test-config', 1)
        ->getAttribute('data-test-config')->toBe('1')
        ->addConfig('test-config-string', 'string')
        ->getAttribute('data-test-config-string')->toBe('string')
        ->addConfig('test-config-float', '1.2')
        ->getAttribute('data-test-config-float')->toBe('1.2')
        ->addConfig('test-config-bool', true)
        ->getAttribute('data-test-config-bool')->toBe(true)
    ;
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
