<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Text;
use MoonShine\Fields\Url;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Url::make('Url');
    $this->item = new class () extends Model {
        public string $url = 'https://cutcode.dev';
    };
});

it('text is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Text::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('url');
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toBe(
            view('moonshine::ui.url', [
                'href' => 'https://cutcode.dev',
                'value' => 'https://cutcode.dev',
            ])->render()
        );
});
