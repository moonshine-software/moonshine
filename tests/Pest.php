<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Factories\CommentFactory;
use MoonShine\Tests\Fixtures\Factories\ItemFactory;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\Tests\TestCase;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;

use function Pest\Laravel\actingAs;

uses(TestCase::class)
    ->in(__DIR__);

function fakeRequest(string $url = '/', string $method = 'GET', array $parameters = [], bool $dispatchRoute = false): void
{
    if (strtolower($method) === 'get') {
        $separator = str_contains($url, '?') ? '&' : '?';
        $url .= $separator . http_build_query($parameters);
    }

    asAdmin()->{strtolower($method)}($url, $parameters);

}

function asAdmin(): TestCase
{
    return actingAs(MoonshineUser::query()->find(1), 'moonshine');
}

function fillFromModel(Field $field, Model $model)
{
    $field->fillData($model);
}

function createItem(int $countItems = 1, int $countComments = 3)
{
    return ItemFactory::new()
        ->count($countItems)
        ->has(
            CommentFactory::new()->count($countComments)
        )
        ->create()
        ->first();
}

function addFieldsToTestResource(array|Field $fields, ?string $setType = null, ?string $getType = null): TestResource
{
    if (! is_array($fields)) {
        $fields = [$fields];
    }

    $setter = is_null($setType) ? 'setTestFields' : 'setTest' . ucfirst($setType);
    $getter = is_null($getType)
        ? (is_null($setType) ? 'getFormFields' : 'get' . ucfirst($setType))
        : 'get' . ucfirst($getType);

    return TestResourceBuilder::new(Item::class)
        ->{$setter}([
            ...app(TestItemResource::class)->{$getter}(),
            ...$fields,
        ]);
}

function exampleFields(): Fields
{
    return Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2'),
    ]);
}
