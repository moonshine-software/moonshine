<?php

use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\FormComponents\ChangeLogFormComponent;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Requests\CrudRequestFactory;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('form-components');

beforeEach(function () {
    $this->resource = TestResourceBuilder::new(
        MoonshineUser::class,
        true
    )->setComponents([
        ChangeLogFormComponent::make('Changelog'),
    ])->setTestFields([
        ID::make(),
        Text::make('Name'),
        Text::make('Email'),
    ]);

    $this->requestData = CrudRequestFactory::new();
});

it('component rendered', function () {
    $user = MoonshineUser::factory()->create();

    $response = asAdmin()->get($this->resource->route('edit', $user->getKey()));

    expect($response)
        ->not
        ->see('Changelog');

    $requestData = $this->requestData->create();

    $email = fake()->email();

    $this->requestData->withEmail($email);

    asAdmin()
        ->put(
            $this->resource->route('update', $user->getKey()),
            $requestData
        )
        ->assertValid()
        ->assertRedirect($this->resource->route('index'));

    $response = asAdmin()->get($this->resource->route('edit', $user->getKey()));

    expect($response)
        ->see('Changelog');
});
