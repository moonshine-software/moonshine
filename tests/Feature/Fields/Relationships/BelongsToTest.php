<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\ImportHandler;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;

uses()->group('model-relation-fields');
uses()->group('belongs-to-field');

beforeEach(function (): void {
    $this->users = MoonshineUser::factory()
        ->count(5)
        ->create();

    $this->item = Item::factory()
        ->create();
});

it('show field on pages', function () {
    $resource = belongsToResource()->setTestFields([
        BelongsTo::make('User', resource: MoonShineUserResource::class),
    ]);

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('User')
        ->assertSee($this->item->user->name)
    ;

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('User')
        ->assertSee($this->item->user->name)
    ;

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee($this->users[0]->name)
        ->assertSee($this->users[1]->name)
        ->assertSee($this->users[2]->name)
        ->assertSee($this->users[3]->name)
        ->assertSee($this->users[4]->name)
        ->assertSee($this->item->user->name)
        ->assertSee('User')
    ;
});

it('belongs to searchable', function () {
    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
            ->searchable()
    );

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee($this->users[0]->name)
        ->assertSee($this->users[1]->name)
        ->assertSee($this->users[2]->name)
        ->assertSee($this->users[3]->name)
        ->assertSee($this->users[4]->name)
        ->assertSee($this->item->user->name)
        ->assertSee('data-search-enabled')
        ->assertSee('User')
    ;
});

it('belongs to asyncsearch', function () {
    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
            ->asyncSearch()
    );

    $asyncUsers = array_values(
        $this->users
            ->filter(fn ($user) => $user->id !== $this->item->user->id)->toArray()
    );

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertDontSee($asyncUsers[0]['name'])
        ->assertDontSee($asyncUsers[1]['name'])
        ->assertDontSee($asyncUsers[2]['name'])
        ->assertDontSee($asyncUsers[3]['name'])
        ->assertSee($this->item->user->name)
        ->assertSee('User')
    ;
});

it('belongs to valuesQuery', function () {

    $id = randomUserId();

    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
            ->valuesQuery(static fn (Builder $query) => $query->where('id', $id))
    );

    $user = $this->users
            ->filter(static fn ($item) => $item->id === $id)->first()
    ;

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee($user->name)
        ->assertSee('User')
    ;
});

it('apply as base', function () {
    $resource = belongsToResource()->setTestFields([
        BelongsTo::make('User', resource: MoonShineUserResource::class),
    ]);

    saveMoonShineUser($resource, $this->item);

    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('User')
        ->assertSee($this->item->user->name)
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
            ->onBeforeApply(static function ($item, $data) {
                $item->name = $item->name . '_' . $data;

                return $item;
            })
    );

    $id = randomUserId();

    $data = ['moonshine_user_id' => $id];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toContain('_' . $id)
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
            ->onAfterApply(static function ($item) {
                $item->name = $item->name . '_' . $item->user->id;

                return $item;
            })
    );

    $id = randomUserId();

    $data = ['moonshine_user_id' => $id];

    asAdmin()->put(
        $resource->getRoute('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toContain('_' . $id)
    ;
});

it('export', function (): void {
    belongsToExport($this->item, randomUserId());
});

it('import', function (): void {

    $id = randomUserId();

    $file = belongsToExport($this->item, $id);

    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class),
        'importFields'
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->getRoute('handler', query: ['handlerUri' => $import->getUriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->moonshine_user_id)
        ->toBe($id)
    ;
});

function belongsToExport(Item $item, int $newId): ?string
{
    $data = ['moonshine_user_id' => $newId];

    $item->moonshine_user_id = $data['moonshine_user_id'];

    $item->save();

    $resource = addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class),
        'exportFields'
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->getRoute('handler', query: ['handlerUri' => $export->getUriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('User')
        ->toContain($item->user->getKey())
    ;

    return $file;
}

function belongsToResource(): TestResource
{
    return addFieldsToTestResource(
        BelongsTo::make('User', resource: MoonShineUserResource::class)
    );
}

function saveMoonShineUser(ModelResource $resource, Model $item): void
{
    $id = randomUserId();
    $data = ['moonshine_user_id' => $id];

    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        $data
    )
        ->assertRedirect();

    $item->refresh();

    $resource->getIndexFields()->each(static function ($field) {
        $field->reset();
    });

    expect($item->moonshine_user_id)
        ->toBe($id)
    ;
}

function randomUserId(): int
{
    return MoonshineUser::query()->where('id', '!=', 1)->inRandomOrder()->first()->id;
}
