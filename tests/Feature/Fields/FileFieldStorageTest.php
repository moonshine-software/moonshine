<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use Pest\Expectation;

uses()->group('fields');
uses()->group('file-field');
uses()->group('file-field-storage');

beforeEach(function (): void {
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

expect()->extend('createResourceWithFiles', function ($categoryResource): Expectation {

    $image = UploadedFile::fake()->image('test-category-image.png');

    $hasManyImage1 = UploadedFile::fake()->image('test-hasmany-1.png');
    $hasManyImage2 = UploadedFile::fake()->image('test-hasmany-2.png');

    $hasManyFile1 = UploadedFile::fake()->image('file-hasmany-1.png');
    $hasManyFile2 = UploadedFile::fake()->image('file-hasmany-2.png');

    $hasManyFile3 = UploadedFile::fake()->image('file-hasmany-3.png');
    $hasManyFile4 = UploadedFile::fake()->image('file-hasmany-4.png');

    expect($categoryResource->getModel())->toBeInstanceOf(Category::class);

    fakeRequest(method: 'POST', parameters: [
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['name' => $image],
        ],
        'images' => [
            ['name' => $hasManyImage1],
            ['name' => $hasManyImage2],
        ],
        'files' => [
            ['name' => [$hasManyFile1, $hasManyFile2]],
            ['name' => [$hasManyFile3, $hasManyFile4]],
        ],
    ]);

    $category = $categoryResource->save(new Category());

    Storage::disk('public')
        ->assertExists('category_images/' . $image->hashName())
        ->assertExists('category_gallery/' . $hasManyImage1->hashName())
        ->assertExists('category_gallery/' . $hasManyImage2->hashName())
        ->assertExists('category_files/' . $hasManyFile1->hashName())
        ->assertExists('category_files/' . $hasManyFile2->hashName())
        ->assertExists('category_files/' . $hasManyFile3->hashName())
        ->assertExists('category_files/' . $hasManyFile4->hashName())
    ;

    $category->load(['image', 'images', 'files']);

    return expect($category->name)
        ->toBeString('Test Name')
        ->and($category->image->name)
        ->toBeString('category_images/' . $image->hashName())
        ->and($category->images)
        ->toBeInstanceOf(Collection::class)
        ->and($category->images->count())
        ->toBeInt(2)
        ->and($category->files->count())
        ->toBeInt(2);
});

it('successful removal from the form', function (): void {
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

it('successful removal from the index', function (): void {
    $avatar = UploadedFile::fake()->image('avatar-to-delete.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());

    $this->field->afterDelete($this->item);

    Storage::disk('public')->assertMissing('files/'.$avatar->hashName());
});

it('successful mass delete files', function (): void {
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

it('checking if file is saved after request', function (): void {
    $avatar = UploadedFile::fake()->image('avatar-to-delete.png');

    expect()->storeAvatarFile($avatar, $this->field, $this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());

    fakeRequest(method: 'POST', parameters: [
        'hidden_avatar' => 'files/'.$avatar->hashName(),
    ]);

    $this->field->save($this->item);

    Storage::disk('public')->assertExists('files/'.$avatar->hashName());
});

it('category create with files', function (): void {
    expect()
        ->createResourceWithFiles($this->categoryResource)
        ->and($this->categoryResource->getItem()->id)
        ->not()->toBeEmpty()
    ;
});

it('category save files after put', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'hidden_image' => [
            ['name' => $category->image->name],
        ],
        'images' => [
            ['id' => $category->images[0]->id],
            ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            ['name' => $category->images[0]->name],
            ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0], $category->files[0]->name[1]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $saveCategory = $this->categoryResource->save($category);

    expect($saveCategory->image->id)->toBe(1)
        ->and($saveCategory->image->name)->toBeString($category->image->name)
        ->and($saveCategory->images[0]->name)->toBeString($category->images[0]->name)
        ->and($saveCategory->images[1]->name)->toBeString($category->images[1]->name)
        ->and($saveCategory->files[0]->name[0])->toBeString($category->files[0]->name[0])
        ->and($saveCategory->files[0]->name[1])->toBeString($category->files[0]->name[1])
        ->and($saveCategory->files[1]->name[0])->toBeString($category->files[1]->name[0])
        ->and($saveCategory->files[1]->name[1])->toBeString($category->files[1]->name[1])
    ;

    Storage::disk('public')
        ->assertExists($category->image->name)
        ->assertExists($category->images[0]->name)
        ->assertExists($category->images[1]->name)
        ->assertExists($category->files[0]->name[0])
        ->assertExists($category->files[0]->name[1])
        ->assertExists($category->files[1]->name[0])
        ->assertExists($category->files[1]->name[1])
    ;
});

it('delete only image from has one', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'images' => [
            ['id' => $category->images[0]->id],
            ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            ['name' => $category->images[0]->name],
            ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0], $category->files[0]->name[1]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('image')->first();

    expect($saveCategory->image)->not()->toBeEmpty()
        ->and($saveCategory->image->name)->toBeEmpty();

    Storage::disk('public')->assertMissing($category->image->name);
});

it('delete file and item from has one', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'images' => [
            ['id' => $category->images[0]->id],
            ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            ['name' => $category->images[0]->name],
            ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0], $category->files[0]->name[1]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('image')->first();

    expect($saveCategory->image)->toBeEmpty();

    Storage::disk('public')->assertMissing($category->image->name);
});

it('delete only image from has many', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'hidden_image' => [
            ['name' => $category->image->name],
        ],
        'images' => [
            0 => ['id' => $category->images[0]->id],
            1 => ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            1 => ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0], $category->files[0]->name[1]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('images')->first();

    expect($saveCategory->images[0])->not()->toBeEmpty()
        ->and($saveCategory->images[0]->name)->toBeEmpty();

    Storage::disk('public')->assertMissing($category->images[0]->name);
});

it('delete item and image from has many', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'hidden_image' => [
            ['name' => $category->image->name],
        ],
        'images' => [
            1 => ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            1 => ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0], $category->files[0]->name[1]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('images')->first();

    expect($saveCategory->images[0]->id)->toBeInt($category->images[1]->id);

    Storage::disk('public')->assertMissing($category->images[0]->name);
});

it('delete only image from has many multiple', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'hidden_image' => [
            ['name' => $category->image->name],
        ],
        'images' => [
            ['id' => $category->images[0]->id],
            ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            ['name' => $category->images[0]->name],
            ['name' => $category->images[1]->name],
        ],
        'files' => [
            ['id' => $category->files[0]->id],
            ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            ['name' => [$category->files[0]->name[0]]],
            ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('files')->first();

    expect($saveCategory->files[0]->name)->toBeArray()
        ->and(is_countable($saveCategory->files[0]->name) ? count($saveCategory->files[0]->name) : 0)->toBeInt(1)
        ->and($saveCategory->files[0]->name[0])->toBeString($category->files[0]->name[0])
    ;

    Storage::disk('public')->assertMissing($category->files[0]->name[1]);
});

it('delete item and image from has many multiple', function (): void {
    expect()->createResourceWithFiles($this->categoryResource);

    $category = $this->categoryResource->getItem();

    fakeRequest(method: 'PUT', parameters: [
        'id' => $category->id,
        'name' => 'Test Name',
        'content' => 'Test Content',
        'image' => [
            ['id' => $category->image->id],
        ],
        'hidden_image' => [
            ['name' => $category->image->name],
        ],
        'images' => [
            ['id' => $category->images[0]->id],
            ['id' => $category->images[1]->id],
        ],
        'hidden_images' => [
            ['name' => $category->images[0]->name],
            ['name' => $category->images[1]->name],
        ],
        'files' => [
            1 => ['id' => $category->files[1]->id],
        ],
        'hidden_files' => [
            1 => ['name' => [$category->files[1]->name[0], $category->files[1]->name[1]]],
        ],
    ]);

    $this->categoryResource->save($category);

    $saveCategory = Category::query()->where('id', $category->id)->with('files')->first();

    expect($saveCategory->files[0]->name)->toBeArray()
        ->and(is_countable($saveCategory->files[0]->name) ? count($saveCategory->files[0]->name) : 0)->toBeInt(1)
        ->and($saveCategory->files[0]->name[0])->toBeString($category->files[1]->name[0])
        ->and($saveCategory->files[0]->name[1])->toBeString($category->files[1]->name[1])
    ;

    Storage::disk('public')->assertMissing($category->files[0]->name[0]);
    Storage::disk('public')->assertMissing($category->files[0]->name[1]);
});
