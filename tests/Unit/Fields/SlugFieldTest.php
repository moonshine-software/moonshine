<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Slug::make('Slug')
        ->from('title')
        ->separator('-');
    $this->item = new class () extends Model {
        public string $title = 'Title';
        public string $slug = 'title';
    };
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('text');
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.input');
});

it('save', function (): void {
    $this->item->title = 'Hello world';

    expect($this->field->save($this->item))
        ->slug
        ->toBe('hello-world')
        ->and($this->field->separator('_')->save($this->item))
            ->slug
            ->toBe('hello_world')
    ;
});
