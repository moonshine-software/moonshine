<?php

declare(strict_types=1);

uses()->group('pages-feature');

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Laravel\Http\Controllers\ProfileController;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\UI\Fields\Image;

beforeEach(function () {
    $this->page = app(ProfilePage::class);
    $this->user = MoonshineUser::query()->find(1);
});

it('successful response', function () {
    asAdmin()->get(toPage($this->page))
        ->assertOk()
        ->assertSee($this->user->name)
        ->assertSee($this->user->email);
});

it('validation error', function () {
    $data = [
        $this->moonshineCore->getConfig()->getUserField('name') => 'New name',
    ];

    asAdmin()->post(
        action([ProfileController::class, 'store']),
        $data
    )
        ->assertSessionHasErrors([
            $this->moonshineCore->getConfig()->getUserField('username'),
        ]);
});

it('successful save', function () {
    $avatar = UploadedFile::fake()->create('avatar.png');
    $data = [
        $this->moonshineCore->getConfig()->getUserField('avatar') => $avatar,
        $this->moonshineCore->getConfig()->getUserField('username') => $this->user->email,
        $this->moonshineCore->getConfig()->getUserField('name') => 'New name',
    ];

    asAdmin()->post(
        action([ProfileController::class, 'store']),
        $data
    )
        ->assertRedirect();

    $this->user->refresh();

    $image = $this->page->getComponents()
        ->onlyFields()
        ->findByClass(Image::class);

    expect($this->user)
        ->avatar
        ->toBe($image->getDir() . '/' . $avatar->hashName())
        ->name
        ->toBe('New name');

    Storage::disk($image->getDisk())
        ->assertExists($image->getDir() . '/' . $avatar->hashName());
});
