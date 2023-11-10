<?php

declare(strict_types=1);

use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;
use MoonShine\MoonShineRouter;
use MoonShine\Resources\MoonShineUserResource;

beforeEach(function () {
    $this->resource = MoonShine::getResourceFromClassName(MoonShineUserResource::class);

    $this->user = MoonshineUser::query()->find(1);
});

it('resource update-column', function () {
    asAdmin()->put(
        MoonShineRouter::to('column.resource.update-column', [
            'resourceItem' => $this->user->getKey(),
            'resourceUri' => $this->resource->uriKey(),
            'field' => 'name',
            'value' => 'New name',
        ])
    )->assertStatus(204);

    $this->user->refresh();

    expect($this->user->name)
        ->toBe('New name')
    ;
});
