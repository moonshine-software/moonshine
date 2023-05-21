<?php

use Illuminate\Http\UploadedFile;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

use function Pest\Laravel\assertModelMissing;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function () {
    Storage::fake('public');

    $this->field = File::make('avatar')
        ->disk('public')
        ->dir('files');

    $this->resource = TestResourceBuilder::new(MoonshineUser::class)
        ->setTestFields(
            [
                ID::make(),
                $this->field,
            ]
        );

    $this->item = MoonshineUser::factory()->create();
});

expect()->extend('storeAvatarFile', function ($avatar, $field, $item) {

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $field->save($item);

    return expect($item->avatar)
        ->toBe('files/'.$avatar->hashName());
});

it('successful stored', function () {
    $avatar = UploadedFile::fake()->image('avatar.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());
});

it('successful stored with original name', function () {
    $avatar = UploadedFile::fake()->image('avatar.png');

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $this->field
        ->keepOriginalFileName()
        ->save($this->item);

    expect($this->item->avatar)
        ->toBe('files/'.$avatar->getClientOriginalName());

    Storage::disk('public')->assertExists('files/'.$avatar->getClientOriginalName());
});

it('store throw allowed extension exception', function () {
    $this->field->allowedExtensions(['gif']);

    $avatar = UploadedFile::fake()->image('avatar.png');

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $this->field->save($this->item);
})->throws(FieldException::class);

it('has one or many method', function () {
    fakeRequest(parameters: [
        'hidden_files' => [
            'hidden_file1.png',
        ],
    ]);

    $avatar = UploadedFile::fake()->image('avatar.png');
    $values = ['avatar' => $avatar];

    expect($this->field->hasManyOrOneSave('hidden_files', $values))
        ->toBe(['avatar' => 'files/'.$avatar->hashName()]);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());
});

it('custom name', function () {
    $avatar = UploadedFile::fake()->image('avatar.png');

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $this->field
        ->customName(function (UploadedFile $file) {
            return 'testing.' . $file->extension();
        })
        ->save($this->item);

    expect($this->item->avatar)
        ->toBe('files/testing.png');

    Storage::disk('public')->assertExists('files/testing.png');
});

it('successful removal from the form', function () {
    $avatar = UploadedFile::fake()->image('avatar-to-delete.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());

    fakeRequest(method: 'POST', parameters: [
        'avatar' => null,
    ]);

    $this->field->save($this->item);

    expect($this->item->avatar)
        ->toBeNull();

    Storage::disk('public')->assertMissing('files/'.$avatar->hashName());
});

it('successful removal from the index', function () {
    $avatar = UploadedFile::fake()->image('avatar-to-delete.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());

    $this->field->afterDelete($this->item);

    Storage::disk('public')->assertMissing('files/'.$avatar->hashName());
});

it('successful mass delete files', function () {
    $users = MoonshineUser::factory(3)->create();

    $avatars = [];

    foreach ($users as $user) {
        $avatar = UploadedFile::fake()->image($user->id.'_avatar-to-delete.png');
        expect()->storeAvatarFile($avatar, $this->field, $user);
        $this->field->save($user);
        $user->save();
        $avatars[] = $avatar;
    }

    $this->resource->massDelete($users->map(fn ($i) => $i->id)->toArray());

    $users->each(fn ($user) => assertModelMissing($user));

    foreach ($avatars as $avatar) {
        Storage::disk('public')->assertMissing('moonshine_users/'.$avatar->hashName());
    }
});

it('checking if file is saved after request', function () {
    $avatar = UploadedFile::fake()->image('avatar-to-delete.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());

    fakeRequest(method: 'POST', parameters: [
        'hidden_avatar' => 'files/'.$avatar->hashName(),
    ]);

    $this->field->save($this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());
});
