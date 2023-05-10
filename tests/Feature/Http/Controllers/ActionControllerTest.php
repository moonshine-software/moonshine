<?php

use MoonShine\Actions\ExportAction;
use MoonShine\Actions\FiltersAction;
use MoonShine\Actions\ImportAction;
use MoonShine\BulkActions\BulkAction;
use MoonShine\FormActions\FormAction;
use MoonShine\ItemActions\ItemAction;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

uses()->group('controllers');
uses()->group('actions');

beforeEach(function () {
    Storage::fake('public');

    $this->resource = TestResourceBuilder::new(model: MoonshineUser::class, addRoutes: true)
        ->setTestActions([
            ExportAction::make('Export'),
            ImportAction::make('Import'),
            FiltersAction::make('Filters'),
        ])
        ->setTestBulkActions([
            BulkAction::make('Delete', static fn ($item) => $item->delete()),
        ])
        ->setTestItemActions([
            ItemAction::make('Delete', static fn ($item) => $item->delete()),
        ])
        ->setTestFormActions([
            FormAction::make('Delete', static fn ($item) => $item->delete()),
        ]);
});

it('export action', function () {
    $action = ExportAction::make('Export')
        ->disk('public')
        ->setResource($this->resource);

    $response = asAdmin()->get(
        $action->url()
    );

    $response->assertOk()
        ->assertDownload("{$this->resource->routeNameAlias()}.xlsx");
});

it('import action', function () {
    $action = ImportAction::make('Import')
        ->disk('public')
        ->setResource($this->resource);

    expect(asAdmin()->post(
        $action->url()
    ))->isSuccessfulOrRedirect();
});

it('bulk action', function () {
    $users = MoonshineUser::factory(10)->create();

    $users->each(fn ($user) => assertModelExists($user));

    asAdmin()->post(
        $this->resource->route('actions.bulk', query: ['index' => 0, 'ids' => $users->implode('id', ';')])
    );

    $users->each(fn ($user) => assertModelMissing($user));
});

it('item action', function () {
    $user = MoonshineUser::factory()->create();

    assertModelExists($user);

    asAdmin()->get(
        $this->resource->route('actions.item', query: ['index' => 0, $this->resource->routeParam() => $user->getKey()])
    );

    assertModelMissing($user);
});

it('form action', function () {
    $user = MoonshineUser::factory()->create();

    assertModelExists($user);

    asAdmin()->get(
        $this->resource->route('actions.form', query: ['index' => 0, $this->resource->routeParam() => $user->getKey()])
    );

    assertModelMissing($user);
});
