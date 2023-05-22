<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');
uses()->group('file-field');
uses()->group('file-field-storage');

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

    $this->relationResource = TestResourceBuilder::new(MoonshineUser::class)
        ->setTestFields(
            [
                ID::make(),
                $this->field,
            ]
        );

    $this->item = MoonshineUser::factory()->create();

    $this->categoryResource = TestResourceBuilder::buildCategoryResource();
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

it('category create with files', function () {

    $image = UploadedFile::fake()->image('test-category-image.png');

    $hasManyImage1 = UploadedFile::fake()->image('test-hasmany-1.png');
    $hasManyImage2 = UploadedFile::fake()->image('test-hasmany-2.png');

    expect($this->categoryResource->getModel())->toBeInstanceOf(Category::class);

    fakeRequest(method: 'POST', parameters: [
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => '', 'name' => $image]
        ],
        'images' => [
            ['id' => '', 'name' => $hasManyImage1],
            ['id' => '', 'name' => $hasManyImage2],
        ]
    ]);

    $this->categoryResource->save(new Category());

    $category = Category::query()->with('image')->first();

    expect($category->name)
        ->toBeString('Test Name')
        ->and($category->image->name)
        ->toBeString('category_images/' . $image->hashName())
        ->and($category->images)
        ->toBeInstanceOf(Collection::class)
        ->and($category->images->count())
        ->toBeInt(2)
    ;

    Storage::disk('public')
        ->assertExists('category_images/' . $image->hashName())
        ->assertExists('category_gallery/' . $hasManyImage1->hashName())
        ->assertExists('category_gallery/' . $hasManyImage2->hashName())
    ;
});