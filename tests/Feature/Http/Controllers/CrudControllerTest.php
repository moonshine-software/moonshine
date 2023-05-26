<?php

use Illuminate\Support\Facades\Event;
use MoonShine\Fields\Text;
use MoonShine\Http\Controllers\CrudController;
use MoonShine\Models\MoonshineUser;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Tests\Fixtures\Requests\CrudRequestFactory;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;

uses()->group('controllers');
uses()->group('crud');

beforeEach(function () {
    $this->user = MoonshineUser::factory()->create();
    $this->resource = $this->moonShineUserResource();
    $this->requestData = CrudRequestFactory::new();
});

it('show index page if authorized', function () {
    $response = asAdmin()
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
    $response = asAdmin()
        ->get($this->resource->route('create'));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseEditView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->resource->getModel());
});

it('show edit page', function () {
    $this->resource->setItem($this->user);

    $response = asAdmin()
        ->get($this->resource->route('edit', $this->user->getKey()));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseEditView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->user);
});

it('show detail page', function () {
    $this->resource->setItem($this->user);

    $response = asAdmin()
        ->get($this->resource->route('show', $this->user->getKey()));

    expect($response)->isSuccessful();

    $response->assertViewIs($this->resource::$baseShowView);
    $response->assertViewHas('resource', $this->resource);
    $response->assertViewHas('item', $this->user);
});

it('successful stored', function () {
    Event::fake();

    $email = fake()->email();

    $this->requestData->withEmail($email);

    assertDatabaseMissing('moonshine_users', [
        'email' => $email,
    ]);

    asAdmin()
        ->post($this->resource->route('store'), $this->requestData->create())
        ->assertValid()
        ->assertRedirect($this->resource->route('index'));

    assertDatabaseHas('moonshine_users', [
        'email' => $email,
    ]);
});

it('validation error on stored', function () {
    $this->requestData->withEmail('');

    asAdmin()
        ->post($this->resource->route('store'), $this->requestData->create())
        ->assertInvalid(['email']);
});

it('successful updated', function () {
    Event::fake();

    $email = fake()->email();

    $this->requestData->withEmail($email);

    assertDatabaseMissing('moonshine_users', [
        'email' => $email,
    ]);

    $requestData = $this->requestData->create();

    asAdmin()
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

    asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $this->requestData->create()
        )
        ->assertInvalid(['email']);
});

it('changed route after save', function () {
    Event::fake();

    $this->resource = TestResourceBuilder::new(get_class($this->user), true)
        ->setTestRouteAfterSave('edit');

    asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $this->requestData->create()
        )
        ->assertValid()
        ->assertRedirect($this->resource->route('edit', $this->user->getKey()));
});

it('successful destroy item', function () {
    assertModelExists($this->user);

    asAdmin()
        ->delete($this->resource->route('destroy', $this->user->getKey()))
        ->assertRedirect($this->resource->route('index'));

    assertModelMissing($this->user);
});

it('successful mass delete items', function () {
    $users = MoonshineUser::factory(10)->create();

    $users->each(fn ($user) => assertModelExists($user));

    asAdmin()
        ->delete($this->resource->route('massDelete'), ['ids' => $users->implode('id', ';')]);

    $users->each(fn ($user) => assertModelMissing($user));
});

it('column updated', function () {
    $item = MoonshineUser::factory()->create(['name' => 'Before']);

    $columnValue = fake()->words(asText: true);

    assertDatabaseMissing('moonshine_users', [
        'name' => $columnValue,
    ]);

    asAdmin()
        ->putJson($this->resource->route('update-column', $item->getKey()), [
            'field' => 'name',
            'value' => $columnValue,
        ])->assertNoContent();

    assertDatabaseHas('moonshine_users', [
        'name' => $columnValue,
    ]);
});

it('precognition responses', function () {
    $headers = [
        'Precognition' => 'true',
        'Accept' => 'application/json',
    ];

    asAdmin()->put(
        $this->resource->route('update', $this->user->getKey()),
        $this->requestData->create(),
        $headers
    )->assertNoContent();

    asAdmin()->put(
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

    asAdmin()->get(
        $this->resource->route('index', query: ['filters' => $data]),
    )->assertOk()->assertSeeText($nameValue);

    asAdmin()->get(
        $this->resource->route('index', query: [
            'filters' => [
                'created_at' => $dateValue,
            ],
        ]),
    )->assertOk()->assertSeeText($nameValue);

    asAdmin()->get(
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

    asAdmin()->get(
        $this->resource->route('index', query: [
            'order' => [
                'field' => 'created_at',
                'type' => 'DESC',
            ],
        ]),
    )->assertOk()->assertSeeTextInOrder($items->toArray());
});

it('search', function () {
    $searchValue = fake()->words(asText: true);
    $notSearchValue = fake()->words(asText: true);

    MoonshineUser::factory()->create([
        'name' => $searchValue,
    ]);

    MoonshineUser::factory()->create([
        'name' => $notSearchValue,
    ]);

    $response = asAdmin()->get(
        $this->resource->route('index', query: ['search' => $searchValue]),
    );

    expect($response)->see($searchValue)
        ->and($response)->not->see($notSearchValue);
});

it('fragment load', function () {
    $response = asAdmin()->getJson(
        $this->resource->route('index'),
        ['X-Fragment' => 'crud-table']
    );

    expect($response)
        ->see('crudTable')
        ->and($response)
        ->not->see('<html');
});

it('query tags', function () {
    MoonshineUser::factory()->create([
        'email' => 'testing@example.com',
    ]);

    MoonshineUser::factory()->create([
        'email' => 'notfound@example.com',
    ]);

    $tag = QueryTag::make('Tag 1', fn () => MoonshineUser::query()->where('email', 'testing@example.com'));

    $resource = TestResourceBuilder::new(MoonshineUser::class, true)
        ->setTestFields([
            Text::make('Email'),
        ])
        ->setTestQueryTags([$tag]);

    $response = asAdmin()->get(
        $resource->route('query-tag', query: ['queryTag' => $tag->uri()]),
    );

    expect($response)
        ->see('testing@example.com')
        ->and($response)
        ->not->see('notfound@example.com');
});
