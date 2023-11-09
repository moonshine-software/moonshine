<?php


use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\ProfilePage;
use MoonShine\Resources\MoonShineProfileResource;

beforeEach(function () {
    $this->resource = new MoonShineProfileResource();
});

it('index', function () {
    $user = MoonshineUser::query()->find(1);

    asAdmin()
        ->get(to_page(ProfilePage::class, $this->resource))
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
        'name' => 'Test name',
        'username' => 'new@mail.ru',
        'password' => '123456',
        'password_repeat' => '123456',
    ];

    asAdmin()
        ->post($this->resource->route('profile.store'), $data)
        ->assertRedirect();

    $user = MoonshineUser::query()->find(1);

    expect($user->name)
        ->toBe('Test name')
        ->and($user->email)
        ->toBe('new@mail.ru')
    ;
});
