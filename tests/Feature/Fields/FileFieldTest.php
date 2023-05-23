<?php

use Illuminate\Http\UploadedFile;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\File;
use MoonShine\Models\MoonshineUser;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function () {
    Storage::fake('public');

    $this->field = File::make('avatar')
        ->disk('public')
        ->dir('files');

    $this->item = MoonshineUser::factory()->create();
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

    expect($this->field->hasManyOrOneSave($avatar))
        ->toBe('files/'.$avatar->hashName());

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
