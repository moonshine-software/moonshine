<?php

use Illuminate\Http\UploadedFile;
use MoonShine\Components\Layout\Box;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\Tabs\Tab;
use MoonShine\Components\Tabs\Tabs;
use MoonShine\Fields\Email;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\TypeCasts\ModelCast;

uses()->group('form-builder');

beforeEach(function () {
    $this->user = MoonshineUser::query()->first();
    $this->imageDir = 'moonshine_users';
    $this->form = FormBuilder::make()
        ->fields([
            Box::make([
                Tabs::make([
                    Tab::make([
                        ID::make(),
                        Text::make('Name'),
                        Email::make('Email'),
                        Image::make('Avatar')->dir($this->imageDir),
                    ]),

                    Tab::make([
                        Password::make('Password'),
                        PasswordRepeat::make('Password confirmation'),
                    ]),
                ]),
            ]),
        ])
        ->fillCast($this->user, ModelCast::make(MoonshineUser::class));
});

it('apply', function () {
    $avatar = UploadedFile::fake()->create('avatar.png');
    $data = [
        'avatar' => $avatar,
        'username' => $this->user->email,
        'name' => 'New name',
    ];

    fakeRequest(parameters: $data);

    $this->form->apply(
        fn (MoonshineUser $user) => $user->save(),
        throw: true
    );

    $this->user->refresh();

    expect($this->user)
        ->avatar
        ->toBe($this->imageDir . '/' . $avatar->hashName())
        ->name
        ->toBe('New name');

    Storage::disk('public')
        ->assertExists($this->imageDir . '/' . $avatar->hashName());
});
