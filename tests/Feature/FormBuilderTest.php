<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Tabs\Tabs;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

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
        ->fillCast($this->user, new ModelCaster(MoonshineUser::class));
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
