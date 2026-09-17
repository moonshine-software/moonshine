<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Crud\Exceptions\FileFieldException;
use MoonShine\Laravel\Applies\Fields\FileModelApply;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Image;

uses()->group('fields', 'file-field');

beforeEach(function (): void {
    Storage::fake('public');
});

it('reads allowed extensions from the configuration repository', function (array|string $extensions, array $expected): void {
    config()->set('moonshine.allowed_extensions', $extensions);

    $configurator = new MoonShineConfigurator(app('config'));

    expect($configurator->getAllowedExtensions())->toBe($expected);
})->with([
    'extensions' => [['png', 'jpg'], ['png', 'jpg']],
    'wildcard string' => ['*', ['*']],
    'wildcard list' => [['*'], ['*']],
    'empty list' => [[], []],
]);

it('allows uploads by default and when the published configuration has no extension setting', function (): void {
    $configurator = new MoonShineConfigurator(new Repository());

    expect($configurator->getAllowedExtensions())->toBe([])
        ->and(config('moonshine.allowed_extensions'))->toBe([]);

    $field = File::make('File');
    $upload = UploadedFile::fake()->create('document.pdf');

    expect($field->getAllowedExtensions())->toBe([])
        ->and(app(FileModelApply::class)->store($field, $upload))->toBe($upload->hashName());

    Storage::disk('public')->assertExists($upload->hashName());
});

it('supports deferred configuration of allowed extensions', function (): void {
    $configurator = $this->moonshineCore->getConfig();
    $configurator->allowedExtensions(static fn (): array => ['png']);

    $field = File::make('File');
    $upload = UploadedFile::fake()->image('photo.png');

    expect((string) $field->render())->toContain('accept=".png"')
        ->and(app(FileModelApply::class)->store($field, $upload))->toBe($upload->hashName());

    Storage::disk('public')->assertExists($upload->hashName());
});

it('uses the effective extensions when rendering and storing uploads', function (
    string $fieldClass,
    array|string $global,
    array|string|null $local,
    string $accept,
    bool $allowed,
): void {
    $this->moonshineCore->getConfig()->allowedExtensions($global);

    $field = $fieldClass::make('File')->dir('uploads');

    if ($local !== null) {
        $field->allowedExtensions($local);
    }

    expect((string) $field->render())->toContain('accept="' . $accept . '"');

    $upload = UploadedFile::fake()->image('photo.png');
    $apply = app(FileModelApply::class);

    if ($allowed) {
        expect($apply->store($field, $upload))->toBe('uploads/' . $upload->hashName());

        Storage::disk('public')->assertExists('uploads/' . $upload->hashName());
    } else {
        expect(fn (): string => $apply->store($field, $upload))
            ->toThrow(FileFieldException::class, 'png not allowed');

        expect(Storage::disk('public')->allFiles())->toBe([]);
    }
})->with(['file' => [File::class], 'image' => [Image::class]])->with([
    'global allow' => [['png', 'jpg'], null, '.png,.jpg', true],
    'global deny' => [['gif'], null, '.gif', false],
    'local allow overrides global deny' => [['gif'], ['png'], '.png', true],
    'local deny overrides global allow' => [['png'], ['gif'], '.gif', false],
    'global wildcard string' => ['*', null, '*/*', true],
    'global wildcard list' => [['*'], null, '*/*', true],
    'global mixed wildcard' => [['gif', '*'], null, '*/*', true],
    'local wildcard string' => [['gif'], '*', '*/*', true],
    'local wildcard list' => [['gif'], ['*'], '*/*', true],
    'local restriction overrides wildcard' => ['*', ['gif'], '.gif', false],
    'empty global list' => [[], null, '*/*', true],
    'explicit empty local list overrides global restriction' => [['gif'], [], '*/*', true],
]);

it('clears the previous accept restriction when switching to a wildcard', function (string $fieldClass): void {
    $this->moonshineCore->getConfig()->allowedExtensions(['gif']);

    $field = $fieldClass::make('File')->allowedExtensions(['png'])->allowedExtensions('*');
    $upload = UploadedFile::fake()->image('photo.jpg');

    expect((string) $field->render())->toContain('accept="*/*"')
        ->and(app(FileModelApply::class)->store($field, $upload))->toBe($upload->hashName());

    Storage::disk('public')->assertExists($upload->hashName());
})->with(['file' => [File::class], 'image' => [Image::class]]);

it('checks the stored extension against the global list', function (string $fieldClass, bool $customName): void {
    $this->moonshineCore->getConfig()->allowedExtensions(['gif']);

    $field = $fieldClass::make('File');
    $field = $customName
        ? $field->customName(static fn (): string => 'shell.php')
        : $field->keepOriginalFileName();
    $upload = UploadedFile::fake()->create('shell.php', 1, 'image/gif');

    expect(fn (): string => app(FileModelApply::class)->store($field, $upload))
        ->toThrow(FileFieldException::class, 'php not allowed');

    expect(Storage::disk('public')->allFiles())->toBe([]);
})->with(['file' => [File::class], 'image' => [Image::class]])->with([false, true]);

it('rejects disallowed uploads through the resource endpoint without replacing existing files', function (string $fieldClass, bool $multiple): void {
    $this->moonshineCore->getConfig()->allowedExtensions(['png']);

    $item = createItem(countComments: 0);
    $column = $multiple ? 'files' : 'file';
    $path = 'uploads/existing.png';
    $original = $multiple ? [$path] : $path;
    $item->update([$column => $original]);
    Storage::disk('public')->put($path, 'existing file');

    $field = $fieldClass::make('Upload', $column)->dir('uploads');

    if ($multiple) {
        $field->multiple();
    }

    $resource = addFieldsToTestResource($field);
    $upload = UploadedFile::fake()->image('photo.gif');

    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        [$column => $multiple ? [$upload] : $upload],
        ['X-Requested-With' => 'XMLHttpRequest'],
    )->assertStatus(500)->assertJsonPath('message', 'gif not allowed');

    $item->refresh();

    expect($multiple ? $item->files->all() : $item->file)->toBe($original)
        ->and(Storage::disk('public')->allFiles())->toBe([$path])
        ->and(Storage::disk('public')->get($path))->toBe('existing file');
})->with(['file' => [File::class], 'image' => [Image::class]])->with([false, true]);

it('applies global extensions through the resource upload endpoint', function (string $fieldClass, bool $multiple): void {
    $this->moonshineCore->getConfig()->allowedExtensions(['png']);

    $item = createItem(countComments: 0);
    $column = $multiple ? 'files' : 'file';
    $field = $fieldClass::make('Upload', $column)->dir('uploads');

    if ($multiple) {
        $field->multiple();
    }

    $resource = addFieldsToTestResource($field);
    $upload = UploadedFile::fake()->image('photo.png');
    $path = 'uploads/' . $upload->hashName();

    asAdmin()->put(
        $resource->getRoute('crud.update', $item->getKey()),
        [$column => $multiple ? [$upload] : $upload],
    )->assertRedirect();

    $item->refresh();

    expect($multiple ? $item->files->all() : $item->file)->toBe($multiple ? [$path] : $path);
    Storage::disk('public')->assertExists($path);
})->with(['file' => [File::class], 'image' => [Image::class]])->with([false, true]);
