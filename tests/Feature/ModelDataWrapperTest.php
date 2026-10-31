<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestSaveHandlerResource;
use MoonShine\Tests\Fixtures\Services\SaveItemHandler;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Fields\Text;

uses()->group('type-casts');

it('fills fields from the current model values', function (): void {
    $model = new Item(['name' => 'Before', 'content' => 'Content']);
    $wrapper = new ModelDataWrapper(model: $model);
    $fields = Fields::make([Text::make('Name'), Text::make('Content')]);

    expect($fields->fillCloned($wrapper->toArray(), $wrapper)->first()->toValue())->toBe('Before');

    $model->name = 'After';

    expect($fields->fillCloned($wrapper->toArray(), $wrapper)->first()->toValue())->toBe('After')
        ->and($wrapper->toArray()['name'])->toBe('After');
});

it('serializes changes made through the wrapper', function (): void {
    $wrapper = new ModelDataWrapper(model: new Item(['name' => 'Before']));
    expect($wrapper->toArray()['name'])->toBe('Before');

    $wrapper->name = 'After';

    expect($wrapper->toArray()['name'])->toBe('After');
});

it('serializes changed relations and visibility on the original model', function (): void {
    $item = createItem(countComments: 1)->load('comments');
    $wrapper = new ModelDataWrapper(model: $item);
    $wrapper->toArray();

    $wrapper->getOriginal()->comments->first()->content = 'Changed comment';
    $wrapper->getOriginal()->makeHidden('name');

    expect($wrapper->toArray()['comments'][0]['content'])->toBe('Changed comment')
        ->and($wrapper->toArray())->not->toHaveKey('name');
});

it('passes applied values to the save handler and persists them', function (): void {
    $item = createItem();
    $handler = new SaveItemHandler();
    app()->instance(SaveItemHandler::class, $handler);
    $resource = app(TestSaveHandlerResource::class)
        ->setTestModel(Item::class)
        ->setTestFields([Text::make('Name')]);
    app()->instance('request', Request::create(uri: '/', method: 'POST', parameters: ['name' => 'Changed name']));

    $resource->save(new ModelDataWrapper(model: $item));

    expect($handler->data['name'])->toBe('Changed name')
        ->and($item->fresh()->name)->toBe('Changed name');
});
