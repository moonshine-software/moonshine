<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\Url as UrlComponent;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Url;

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
    expect($this->field->getAttributes()->get('type'))
        ->toBe('url');
});

it('index view value', function (): void {
    expect((string) $this->field->preview())
        ->toBe(
            (string) UrlComponent::make('https://cutcode.dev', 'https://cutcode.dev')
                ->render()
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
