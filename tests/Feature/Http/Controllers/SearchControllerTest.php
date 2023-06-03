<?php

use MoonShine\Fields\BelongsTo;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('controllers');
uses()->group('search');

beforeEach(function (): void {
    $this->resource = TestResourceBuilder::new(
        MoonshineUser::class,
        true
    )->setTestFields([
        BelongsTo::make('Moonshine user role')
            ->asyncSearch(
                'name',
                asyncSearchValueCallback: fn ($item): string => $item->id . $item->name
            ),
    ]);
});

it('successful find item', function (): void {
    $role = MoonshineUserRole::factory()->create();

    asAdmin()->getJson(route('moonshine.search.relations', [
        'resource' => $this->resource->uriKey(),
        'column' => 'moonshine_user_role_id',
        'query' => str($role->name)->substr(0, 3)->value(),
    ]))
        ->assertJsonFragment([$role->id => $role->id . $role->name])
        ->assertJsonStructure([$role->id]);
});
