<?php

use MoonShine\Http\Controllers\CrudController;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Requests\CrudRequestFactory;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('controllers');

beforeEach(function () {
    $this->user = MoonshineUser::factory()->create();
    $this->resource = $this->moonShineUserResource();
    $this->requestData = CrudRequestFactory::new();
});

it('show index page if authorized', function () {
    $response = $this->asAdmin()
        ->get($this->resource->route('index'));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseIndexView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('resources', $this->resource->paginate());
});

it('show index page if not authorized', function () {
    $response = $this->get(action([CrudController::class, 'index']));

    expect($response)->isRedirect();

    $response->assertRedirect(route('moonshine.login'));
});

it('show create page', function () {
    $response = $this->asAdmin()
        ->get($this->resource->route('create'));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseEditView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->resource->getModel());
});

it('show edit page', function () {
    $this->resource->setItem($this->user);

    $response = $this->asAdmin()
        ->get($this->resource->route('edit', $this->user->getKey()));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseEditView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->user);
});

it('show detail page', function () {
    $this->resource->setItem($this->user);

    $response = $this->asAdmin()
        ->get($this->resource->route('show', $this->user->getKey()));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseShowView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->user);
});

it('successful stored', function () {
    $email = fake()->email();

    $this->requestData->withEmail($email);

    $this->assertDatabaseMissing('moonshine_users', [
        'email' => $email,
    ]);

    $this->asAdmin()
        ->post($this->resource->route('store'), $this->requestData->create())
        ->assertValid()
        ->assertRedirect($this->resource->route('index'));

    $this->assertDatabaseHas('moonshine_users', [
        'email' => $email,
    ]);
});

it('validation error on stored', function () {
    $this->requestData->withEmail('');

    $this->asAdmin()
        ->post($this->resource->route('store'), $this->requestData->create())
        ->assertInvalid(['email']);
});

it('successful updated', function () {
    $email = fake()->email();

    $this->requestData->withEmail($email);

    $this->assertDatabaseMissing('moonshine_users', [
        'email' => $email,
    ]);

    $requestData = $this->requestData->create();

    $this->asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $requestData
        )
        ->assertValid()
        ->assertRedirect($this->resource->route('index'));

    $updatedUser = $this->user->refresh();

    expect($updatedUser)
        ->email->toBe($requestData['email'])
        ->name->toBe($requestData['name']);
});

it('validation error on updated', function () {
    $this->requestData->withEmail('');

    $this->asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $this->requestData->create()
        )
        ->assertInvalid(['email']);
});

it('changed route after save', function () {
    $this->resource = TestResourceBuilder::new(get_class($this->user), true)
        ->setTestRouteAfterSave('edit');

    $this->asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $this->requestData->create()
        )
        ->assertValid()
        ->assertRedirect($this->resource->route('edit', $this->user->getKey()));
});

it('successful destroy item', function () {
    $this->assertModelExists($this->user);

    $this->asAdmin()
        ->delete($this->resource->route('destroy', $this->user->getKey()))
        ->assertRedirect($this->resource->route('index'));

    $this->assertModelMissing($this->user);
});

it('successful mass delete items', function () {
    $users = MoonshineUser::factory(10)->create();

    $users->each(fn($user) => $this->assertModelExists($user));

    $this->asAdmin()
        ->delete($this->resource->route('massDelete'), ['ids' => $users->implode('id', ';')]);

    $users->each(fn($user) => $this->assertModelMissing($user));
});

it('column updated', function () {
    $columnValue = fake()->words(asText: true);

    $this->assertDatabaseMissing('moonshine_users', [
        'name' => $columnValue,
    ]);

    $this->asAdmin()
        ->putJson(route('moonshine.update-column'), [
            'model' => $this->user::class,
            'key' => $this->user->getKey(),
            'field' => 'name',
            'value' => $columnValue,
        ])->assertNoContent();

    $this->assertDatabaseHas('moonshine_users', [
        'name' => $columnValue,
    ]);
});

it('precognition responses', function () {
    $headers = [
        'Precognition' => 'true',
        'Accept' => 'application/json',
    ];

    $this->asAdmin()->put(
        $this->resource->route('update', $this->user->getKey()),
        $this->requestData->create(),
        $headers
    )->assertNoContent();

    $this->asAdmin()->put(
        $this->resource->route('update', $this->user->getKey()),
        [],
        $headers
    )->assertJsonStructure(['errors' => ['moonshine_user_role_id']]);
});

it('filtered', function () {
    $nameValue = fake()->word();
    $dateValue = fake()->date();

    $data = [
        'name' => $nameValue,
        'created_at' => $dateValue,
    ];

    MoonshineUser::factory()->create($data);

    $this->asAdmin()->get(
        $this->resource->route('index', query: ['filters' => $data]),
    )->assertOk()->assertSeeText($nameValue);

    $this->asAdmin()->get(
        $this->resource->route('index', query: [
            'filters' => [
                'created_at' => $dateValue,
            ],
        ]),
    )->assertOk()->assertSeeText($nameValue);

    $this->asAdmin()->get(
        $this->resource->route('index', query: [
            'filters' => ['created_at' => fake()->date()],
        ]),
    )->assertOk()->assertSeeText(__('moonshine::ui.notfound'));
});

it('sorted', function () {
    $items = $this->resource
        ->query()
        ->orderByDesc('created_at')
        ->pluck('email');

    $this->asAdmin()->get(
        $this->resource->route('index', query: [
            'order' => [
                'field' => 'created_at',
                'type' => 'DESC'
            ],
        ]),
    )->assertOk()->assertSeeTextInOrder($items->toArray());
});

it('search', function () {
    $searchValue = fake()->words(asText: true);
    $notSearchValue = fake()->words(asText: true);

    MoonshineUser::factory()->create([
        'name' => $searchValue
    ]);

    MoonshineUser::factory()->create([
        'name' => $notSearchValue
    ]);

    $response = $this->asAdmin()->get(
        $this->resource->route('index', query: ['search' => $searchValue]),
    );

    expect($response)->see($searchValue)
        ->and($response)->not->see($notSearchValue);
});

it('fragment load', function () {
    $response = $this->asAdmin()->getJson(
        $this->resource->route('index'),
        ['X-Fragment' => 'crud-table']
    );

    expect($response)
        ->see('crudTable')
        ->and($response)
        ->not->see('<html');
});
