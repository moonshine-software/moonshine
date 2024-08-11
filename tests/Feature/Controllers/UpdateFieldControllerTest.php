<?php

declare(strict_types=1);

use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestHasManyCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;

beforeEach(function () {
    $this->resource = $this->moonshineCore->getResources()->findByClass(MoonShineUserResource::class);

    $this->user = MoonshineUser::query()->find(1);
});

it('update through column', function () {
    asAdmin()->put(
        $this->moonshineCore->getRouter()->to('update-field.through-column', [
            'resourceItem' => $this->user->getKey(),
            'resourceUri' => $this->resource->getUriKey(),
            'field' => 'name',
            'value' => 'New name',
        ])
    )->assertStatus(204);

    $this->user->refresh();

    expect($this->user->name)
        ->toBe('New name')
    ;
});

it('update through relation', function () {
    $resource = TestResourceBuilder::new(Item::class);
    $item = createItem(1, 1);
    $comment = $item->comments[0];

    $resource->setTestFields([
        ID::make(),
        HasMany::make('Comments title', 'comments', resource: TestHasManyCommentResource::class),
    ]);

    asAdmin()->put(
        $this->moonshineCore->getRouter()->to('update-field.through-relation', [
            'resourceItem' => $comment->getKey(),
            'resourceUri' => $resource->getUriKey(),
            'pageUri' => 'form-page',
            '_relation' => 'comments',
            'field' => 'active',
            'value' => '0',
        ])
    )->assertStatus(204);

    $comment->refresh();

    expect($comment->active)
        ->toBe(0)
    ;
});
