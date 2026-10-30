<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Laravel\Http\Controllers\CrudController;
use MoonShine\Laravel\Http\Requests\Resources\DeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\MassDeleteFormRequest;
use MoonShine\Laravel\Http\Requests\Resources\UpdateFormRequest;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Pages\TestIndexPage;
use MoonShine\Tests\Fixtures\Pages\TestValidationFormPage;
use MoonShine\Tests\Fixtures\Resources\Crud\TestRefreshingCrudResource;
use MoonShine\Tests\Fixtures\Resources\TestCrudResponseResource;
use MoonShine\UI\Fields\Text;

mutates(CrudController::class);

beforeEach(function (): void {
    $this->item = createItem(countComments: 0);
    $this->resource = app(TestCrudResponseResource::class)
        ->setTestModel(Item::class)
        ->setTestFields([Text::make('Name', 'name')->refreshAfterApply()]);

    $this->moonshineCore->resources([$this->resource]);
});

it('returns a json deletion response for either ajax or accept headers', function (string $route, array $headers): void {
    $url = $this->resource->getRoute($route, $this->item->getKey(), query: ['pageUri' => PageType::FORM->value]);

    asAdmin()->withHeaders($headers)->delete($url, ['ids' => [$this->item->getKey()]])
        ->assertOk()
        ->assertJsonPath('message', __('moonshine::ui.deleted'))
        ->assertJsonPath('messageType', ToastType::SUCCESS->value)
        ->assertJsonPath('activePage', PageType::INDEX->value);

    $this->assertDatabaseMissing('items', ['id' => $this->item->getKey()]);
})->with(['single' => 'crud.destroy', 'mass' => 'crud.massDelete'])->with([
    'ajax only' => [['X-Requested-With' => 'XMLHttpRequest']],
    'json only' => [['Accept' => 'application/json']],
]);

it('flashes a success notification after a synchronous write', function (string $operation): void {
    $client = asAdmin();

    $response = match ($operation) {
        'save' => $client->put($this->resource->getRoute('crud.update', $this->item->getKey()), ['name' => 'Saved name']),
        'delete' => $client->delete($this->resource->getRoute('crud.destroy', $this->item->getKey())),
        'mass delete' => $client->delete($this->resource->getRoute('crud.massDelete'), ['ids' => [$this->item->getKey()]]),
    };

    $response->assertRedirect()->assertSessionHas('toast', static fn (array $toast): bool =>
        $toast['type'] === ToastType::SUCCESS->value
        && $toast['message'] === __($operation === 'save' ? 'moonshine::ui.saved' : 'moonshine::ui.deleted'));
})->with(['save', 'delete', 'mass delete']);

it('keeps the requested redirect when creating a record', function (bool $modal, ?string $redirect): void {
    $this->resource->modalCreation($modal);
    $data = ['name' => 'Fresh record'];

    if ($redirect !== null) {
        $data['_redirect'] = $redirect;
    }

    $response = asAdmin()->postJson($this->resource->getRoute('crud.store'), $data)->assertCreated();

    if ($redirect !== null) {
        $response->assertJsonPath('redirect', $redirect);
    } elseif ($modal) {
        expect($response->json())->not->toHaveKey('redirect');
    } else {
        $item = Item::query()->where('name', 'Fresh record')->sole();
        $response->assertJsonPath('redirect', $this->resource->getFormPageUrl($item->getKey()));
    }
})->with([
    'form redirect' => [false, null],
    'modal without redirect' => [true, null],
    'explicit form redirect' => [false, '/after-save'],
    'explicit modal redirect' => [true, '/after-modal-save'],
]);

it('returns refreshed field html for the correct form after saving', function (bool $create, ?string $relation): void {
    $data = ['name' => 'Fresh record'];

    if ($relation !== null) {
        $data['_relation_name'] = $relation;
    }

    $url = $this->resource->getRoute($create ? 'crud.store' : 'crud.update', $create ? null : $this->item->getKey(), query: [
        'pageUri' => PageType::INDEX->value,
    ]);
    $response = $create ? asAdmin()->postJson($url, $data) : asAdmin()->putJson($url, $data);
    $response->assertStatus($create ? 201 : 200)->assertJsonPath('activePage', PageType::FORM->value);

    $fields = $response->json('htmlData');
    $suffix = $relation === null ? '' : '-' . $relation;

    expect($fields)->toHaveCount(1)
        ->and($fields[0]['selector'])->toBe(".form-resource-{$this->resource->getUriKey()}$suffix [data-field-selector='name']")
        ->and($fields[0]['htmlMode'])->toBe('outer_html')
        ->and($fields[0]['html'])->toBeString()->toContain('value="Fresh record"');
})->with(['create' => true, 'update' => false])->with(['main form' => null, 'relation form' => 'comments']);

it('refreshes fields from the saved result of a non eloquent resource', function (): void {
    $resource = app(TestRefreshingCrudResource::class);
    $this->moonshineCore->resources([$resource]);

    $response = asAdmin()->postJson($resource->getRoute('crud.store'), ['content' => 'Saved through adapter'])
        ->assertCreated();

    expect($response->json('htmlData.0.html'))->toBeString()->toContain('value="Saved through adapter"');
    $response->assertJsonPath('redirect', $resource->getFormPageUrl(1));
});

it('validates a precognitive request without saving it', function (bool $create, bool $valid): void {
    $this->resource->setTestPages([TestIndexPage::class, TestValidationFormPage::class, DetailPage::class]);
    $originalName = $this->item->name;
    $count = Item::query()->count();
    $url = $this->resource->getRoute($create ? 'crud.store' : 'crud.update', $create ? null : $this->item->getKey());
    $client = asAdmin()->withHeaders(['Precognition' => 'true', 'Precognition-Validate-Only' => 'name']);
    $data = ['name' => $valid ? 'Valid' : 'This name is too long'];
    $response = $create ? $client->postJson($url, $data) : $client->putJson($url, $data);

    if ($valid) {
        $response->assertNoContent()->assertHeader('Precognition-Success', 'true');
    } else {
        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    expect(Item::query()->count())->toBe($count)
        ->and($this->item->refresh()->name)->toBe($originalName);
})->with(['create' => true, 'update' => false])->with(['valid' => true, 'invalid' => false]);

it('reports a missing resource when a resource form request is resolved outside resource routes', function (string $requestClass): void {
    Route::post('/form-request-without-resource', static function () use ($requestClass) {
        app($requestClass);

        return response()->noContent();
    });
    $this->withoutExceptionHandling();

    expect(fn () => asAdmin()->post('/form-request-without-resource'))->toThrow(ResourceException::class);
})->with([UpdateFormRequest::class, DeleteFormRequest::class, MassDeleteFormRequest::class]);

it('preserves explicit redirects in json deletion responses', function (string $route): void {
    asAdmin()->deleteJson($this->resource->getRoute($route, $this->item->getKey()), [
        'ids' => [$this->item->getKey()],
        '_redirect' => '/after-delete',
    ])->assertOk()->assertJsonPath('redirect', '/after-delete');
})->with(['single' => 'crud.destroy', 'mass' => 'crud.massDelete']);

it('updates a resource without a form page through the json endpoint', function (): void {
    $this->resource->setTestPages([TestIndexPage::class]);

    asAdmin()->putJson($this->resource->getRoute('crud.update', $this->item->getKey()), [
        'name' => 'Saved without a form page',
    ])->assertOk();

    expect($this->item->refresh()->name)->toBe('Saved without a form page');
});
