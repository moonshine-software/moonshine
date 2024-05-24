<?php

declare(strict_types=1);

use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Switcher;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestHasManyCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function () {
    $this->item = createItem();
    $this->field = Switcher::make('Active')->updateOnPreview(url: fn () => '/');
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource(
        []
    )->setTestFields([$this->field]);


    asAdmin()->get(
        toPage(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;

    asAdmin()->get(
        toPage(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;

    asAdmin()->get(
        toPage(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Active')
        ->assertSee('form-switcher')
    ;
});

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        [$this->field]
    );

    $data = ['active' => 1];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(true)
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        [Switcher::make('Active')
            ->onBeforeApply(function ($item) {
                $item->name = 'Switcher';

                return $item;
            })]
    );

    $data = ['active' => 0];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Switcher')
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        [Switcher::make('Active')
            ->onAfterApply(function ($item, $data) {
                $item->name = 'Switcher';

                return $item;
            })]
    );

    $data = ['active' => 1];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe('Switcher')
        ->and($this->item->active)
        ->toBe(true)
    ;
});

it('apply as base with default', function () {
    $resource = addFieldsToTestResource(
        [Switcher::make('Active')->default(1)]
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(true)
    ;
});

it('resource update column', function () {

    $resource = TestResourceBuilder::new(Item::class);

    $field = Switcher::make('Active')->default(1)->updateOnPreview(resource: $resource);

    $resource->setTestFields([
        ...(new TestItemResource())->indexFields(),
        ...[$field],
    ]);

    $this->item->active = false;
    $this->item->save();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(false);

    asAdmin()->put(
        $this->router->to('column.resource.update-column', [
            'resourceItem' => $this->item->getKey(),
            'resourceUri' => $resource->uriKey(),
            'field' => 'active',
            'value' => 1,
        ])
    )->assertStatus(204);

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(true);
});

it('relation update column', function () {
    $comment = $this->item->comments->first();

    $resource = new TestItemResource();

    expect($comment->active)
        ->toBe(1);

    asAdmin()->put(
        $this->router->to(
            'column.relation.update-column',
            [
                'resourceItem' => $comment->getKey(),
                'resourceUri' => $resource->uriKey(),
                'pageUri' => $resource->formPage()->uriKey(),
                '_relation' => 'comments',
                'field' => 'active',
                'value' => 0,
            ]
        )
    )
        ->assertStatus(204);

    $this->item->refresh();

    $comment = $this->item->comments->first();

    expect($comment->active)
        ->toBe(0);
});

it('relation update column in resource', function () {

    $comment = $this->item->comments->first();

    expect($comment->active)
        ->toBe(1);

    $resource = TestResourceBuilder::new(Item::class);

    fakeRequest(toPage(
        $resource->formPage()->uriKey(),
        $resource,
        [
            'resourceItem' => $this->item->getKey(),
        ]
    ));

    $resource->setTestFields([
        ID::make(),
        HasMany::make('Comments title', 'comments', resource: new TestHasManyCommentResource()),
    ]);

    asAdmin()->put(
        $this->router->to(
            'column.relation.update-column',
            [
                'resourceItem' => $comment->getKey(),
                'resourceUri' => $resource->uriKey(),
                'pageUri' => $resource->formPage()->uriKey(),
                '_relation' => 'comments',
                'field' => 'active',
                'value' => 0,
            ]
        )
    )
        ->assertStatus(204);

    $this->item->refresh();

    $comment = $this->item->comments->first();

    expect($comment->active)
        ->toBe(0);
});

function switcherExport(Item $item): ?string
{
    $data = ['active' => 0];

    $item->active = $data['active'];

    $item->save();

    $resource = addFieldsToTestResource(
        [Switcher::make('Active')],
        'exportFields'
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Active', '0');

    return $file;
}

it('export', function (): void {
    switcherExport($this->item);
});

it('import', function (): void {

    $file = switcherExport($this->item);

    $resource = addFieldsToTestResource(
        [Switcher::make('Active')],
        'importFields'
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->active)
        ->toBe(false)
    ;
});
