<?php


use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\ProfilePage;

it('index', function () {
    $user = MoonshineUser::query()->find(1);

    asAdmin()
        ->get(
            toPage(
                moonshineConfig()->getPage('profile', ProfilePage::class)
            )
        )
        ->assertSee($user->name)
        ->assertSee($user->email)
        ->assertSee('Avatar')
        ->assertSee('Change password')
        ->assertSee('Change password')
        ->assertSee('Password')
        ->assertSee('Repeat password')
        ->assertOk();
});

it('store', function () {
    $data = [
        moonshineConfig()->getUserField('name') => 'Test name',
        moonshineConfig()->getUserField('username') => 'new@mail.ru',
        moonshineConfig()->getUserField('password') => '123456',
        'password_repeat' => '123456',
    ];

    asAdmin()
        ->post(action([ProfileController::class, 'store']), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $user = MoonshineUser::query()->find(1);

    expect($user->name)
        ->toBe('Test name')
        ->and($user->email)
        ->toBe('new@mail.ru')
    ;
});
