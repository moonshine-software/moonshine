<?php

use Illuminate\Support\Facades\Route;
use MoonShine\Tests\Fixtures\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\get;

it('does not generate logs for non MoonShine request', function () {
    $user = User::factory()->create([
        'name' => 'John',
    ]);
    asAdmin();
    actingAs($user);

    assertDatabaseEmpty('moonshine_change_logs');

    Route::get('/change-me', function () use ($user) {
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
