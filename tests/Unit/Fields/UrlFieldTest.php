<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Text;
use MoonShine\Fields\Url;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Url::make('Url');
    $this->item = new class () extends Model {
        public string $url = 'https://cutcode.dev';
    };
    fillFromModel($this->field, $this->item);
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
    expect($this->field->preview())
        ->toBe(
            view('moonshine::ui.url', [
                'href' => 'https://cutcode.dev',
                'value' => 'https://cutcode.dev',
            ])->render()
        );
});

it('apply', function (): void {
    $data = ['url' => 'https://cutcode.dev'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'url',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->url
        ->toBe($data['url'])
    ;
});
