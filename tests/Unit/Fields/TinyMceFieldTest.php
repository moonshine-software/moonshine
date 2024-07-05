<?php

declare(strict_types=1);

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

it('tinymce methods', function (): void {
    expect($this->field)
        ->commentAuthor('test')
        ->commentAuthor->toBe('test')
        ->menubar('test')
        ->menubar->toBe('test')
        ->addConfig('test-config', 1)
        ->config->toContain(1)
        ->addConfig('test-config-string', 'string')
        ->config->toContain('string')
        ->addConfig('test-config-float', 1.2)
        ->config->toContain(1.2)
        ->addConfig('test-config-bool', true)
        ->config->toContain(true)
        ->addConfig('test-config-array', ['array' => 'array'])
        ->config->toContain(['array' => 'array'])
        ->addConfig('test-config-json', json_encode([['json' => 'json']]))
        ->config->toContain([['json' => 'json']])
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
