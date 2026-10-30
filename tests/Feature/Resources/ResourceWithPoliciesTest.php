<?php

declare(strict_types=1);

use MoonShine\Crud\Buttons\CreateButton;
use MoonShine\Crud\Buttons\MassDeleteButton;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Tests\Fixtures\Models\Comment;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\TestIndexPage;
use MoonShine\Tests\Fixtures\Pages\TestValidationFormPage;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestReadOnlyResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

uses()->group('resources-feature');
uses()->group('resources-policies');

mutates(DeleteFormRequest::class, MassDeleteFormRequest::class, UpdateFormRequest::class);

beforeEach(function (): void {

    $this->item = createItem(1, 1);

    $this->resource = TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ID::make()->sortable(),
            Text::make('Name', 'name')->sortable(),
            HasMany::make(
                'Comments',
                resource: app(TestCommentResource::class)->setTestPolicy(true)
            )->creatable(),
        ])
        ->setTestPolicy(true)
    ;
});

it('policies in index', function () {
    expect($this->resource->isWithPolicy())
        ->toBeTrue();

    asAdmin()->get(
        $this->resource->getIndexPageUrl()
    )
        ->assertOk()
        ->assertSeeHtml(CreateButton::for('Create', $this->resource))
        ->assertSeeHtml(MassDeleteButton::for($this->resource, modalName: $this->resource->getUriKey() . '-mass-delete-modal'))
    ;

});

it('mass delete respects the resource policy', function (bool $allowed): void {
    if (! $allowed) {
        $role = MoonshineUserRole::query()->create(['name' => 'Without delete permission']);

        MoonshineUser::query()->where('id', 1)->update([
            'moonshine_user_role_id' => $role->getKey(),
        ]);
    }

    $response = asAdmin()->delete(
        $this->resource->getRoute('crud.massDelete'),
        ['ids' => [$this->item->getKey()]],
    );

    if ($allowed) {
        $response->assertRedirect();
        $this->assertDatabaseMissing('items', ['id' => $this->item->getKey()]);
    } else {
        $response->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $this->item->getKey()]);
    }
})->with([
    'allowed' => true,
    'denied' => false,
]);

it('single delete respects the resource policy', function (bool $allowed): void {
    if (! $allowed) {
        $role = MoonshineUserRole::query()->create(['name' => 'Without delete permission']);
        MoonshineUser::query()->whereKey(1)->update(['moonshine_user_role_id' => $role->getKey()]);
    }

    $response = asAdmin()->delete($this->resource->getRoute('crud.destroy', $this->item->getKey()));

    if ($allowed) {
        $response->assertRedirect();
        $this->assertDatabaseMissing('items', ['id' => $this->item->getKey()]);
    } else {
        $response->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $this->item->getKey()]);
    }
})->with(['allowed' => true, 'denied' => false]);

it('update respects the resource policy', function (bool $allowed): void {
    $originalName = $this->item->name;

    if (! $allowed) {
        MoonshineUser::query()->whereKey(1)->update(['name' => 'Policies test']);
    }

    $response = asAdmin()->put($this->resource->getRoute('crud.update', $this->item->getKey()), [
        'name' => 'Updated through policy',
    ]);

    if ($allowed) {
        $response->assertRedirect();
    } else {
        $response->assertForbidden();
    }

    expect($this->item->refresh()->name)->toBe($allowed ? 'Updated through policy' : $originalName);
})->with(['allowed' => true, 'denied' => false]);

it('rejects update when the resource action is disabled without a policy', function (): void {
    $originalName = $this->item->name;
    $resource = app(TestReadOnlyResource::class)->setTestModel(Item::class)->setTestPolicy(false);
    $this->moonshineCore->resources([$resource]);

    asAdmin()->put($resource->getRoute('crud.update', $this->item->getKey()), [
        'name' => 'Forbidden update',
    ])->assertForbidden();

    expect($this->item->refresh()->name)->toBe($originalName);
});

it('policy in has many', function () {
    $comment = Comment::query()->first();

    asAdmin()->get(
        $this->resource->getFormPageUrl($this->item->id)
    )
        ->assertOk()
        ->assertSee($comment->content)
        ->assertDontSee('has-many-modal-test-comment-resource-comments-create')
        ->assertSee('has-many-modal-test-comment-resource-comments-mass-delete')
    ;

});

it('policies index forbidden', function () {
    MoonshineUser::query()->where('id', 1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()->get(
        $this->resource->getIndexPageUrl()
    )->assertForbidden();
});

it('policy denied index is forbidden by crud json endpoint', function () {
    MoonshineUser::query()->where('id', 1)->update([
        'name' => 'Policies test',
    ]);

    asAdmin()
        ->withHeader('Accept', 'application/json')
        ->get($this->resource->getRoute('crud.index'))
        ->assertForbidden();
});

it('policies in detail', function () {
    asAdmin()->get(
        $this->resource->getDetailPageUrl($this->item->id)
    )->assertForbidden();
});

it('policy denied detail is forbidden by crud json endpoint', function () {
    asAdmin()
        ->withHeader('Accept', 'application/json')
        ->get($this->resource->getRoute('crud.show', $this->item->id))
        ->assertForbidden();
});

it('rejects invalid updates before writing to the database', function (): void {
    $originalName = $this->item->name;
    $this->resource->setTestPages([TestIndexPage::class, TestValidationFormPage::class, DetailPage::class]);

    asAdmin()->putJson($this->resource->getRoute('crud.update', $this->item->getKey()), [
        'name' => 'This value exceeds the configured maximum',
    ])->assertUnprocessable()->assertJsonValidationErrors('name');

    expect($this->item->refresh()->name)->toBe($originalName);
});

it('rejects malformed mass delete selections without deleting records', function (array $payload): void {
    asAdmin()->deleteJson($this->resource->getRoute('crud.massDelete'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('ids');

    $this->assertDatabaseHas('items', ['id' => $this->item->getKey()]);
})->with([
    'missing' => [[]],
    'empty' => [['ids' => []]],
    'not an array' => [['ids' => 'invalid']],
]);
