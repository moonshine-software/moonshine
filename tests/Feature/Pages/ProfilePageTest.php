<?php

declare(strict_types=1);

uses()->group('pages-feature');

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Fields\Image;
use MoonShine\Http\Controllers\ProfileController;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\ProfilePage;

beforeEach(function () {
    $this->page = new ProfilePage();
    $this->user = MoonshineUser::query()->find(1);
});

it('successful response', function () {
    asAdmin()->get(to_page($this->page))
        ->assertOk()
        ->assertSee($this->user->name)
        ->assertSee($this->user->email);
});

it('validation error', function () {
    $data = [
        'name' => 'New name',
    ];

    asAdmin()->post(
        action([ProfileController::class, 'store']),
        $data
    )
        ->assertSessionHasErrors(['username']);
});

it('successful save', function () {
    $avatar = UploadedFile::fake()->create('avatar.png');
    $data = [
        'avatar' => $avatar,
        'username' => $this->user->email,
        'name' => 'New name',
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
