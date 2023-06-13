<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\User;
use MoonShine\Tests\Fixtures\Requests\CrudRequestFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses()->group('controllers');
uses()->group('crud');

beforeEach(function (): void {
    $this->user = MoonshineUser::factory()->create();
    $this->resource = $this->moonShineUserResource();
    $this->requestData = CrudRequestFactory::new();
});

it('logs stored record', function (): void {
    $email = fake()->email();
    $this->requestData->withEmail($email);

    assertDatabaseEmpty('moonshine_change_logs');

    asAdmin()
        ->post($this->resource->route('store'), $this->requestData->create())
        ->assertValid();

    assertDatabaseHas('moonshine_change_logs', [
        'changelogable_type' => MoonshineUser::class,
        'changelogable_id' => MoonshineUser::query()->where('email', $email)->first()->id,
    ]);
});

it('logs updated record', function (): void {
    $email = fake()->email();
    $this->requestData->withEmail($email);

    assertDatabaseEmpty('moonshine_change_logs');

    $requestData = $this->requestData->create();

    asAdmin()
        ->put(
            $this->resource->route('update', $this->user->getKey()),
            $requestData
        )
        ->assertValid();

    assertDatabaseHas('moonshine_change_logs', [
        'changelogable_type' => MoonshineUser::class,
        'changelogable_id' => $this->user->id,
    ]);
});

it('does not generate logs for non MoonShine request', function (): void {
    $user = User::factory()->create([
        'name' => 'John',
    ]);
    asAdmin();
    actingAs($user);

    assertDatabaseEmpty('moonshine_change_logs');

    Route::get('/change-me', static function () use ($user): void {
        $user->name = 'Nick';
        $user->save();
    })
        ->middleware(['auth', 'web'])
        ->name('change-me');

    get(route('change-me'));

    assertDatabaseEmpty('moonshine_change_logs');

    expect(User::find($user->id)->name)
        ->toBe('Nick');
});
