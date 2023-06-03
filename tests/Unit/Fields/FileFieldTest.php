<?php

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\File;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function (): void {
    $this->field = File::make('File')
        ->disk('public')
        ->dir('files');

    $this->fieldMultiple = File::make('Files')
        ->multiple()
        ->disk('public')
        ->dir('files');

    $this->item = new class () extends Model {
        public string $file = 'files/file.pdf';
        public string $files = '["files/file1.pdf", "files/file2.pdf"]';

        protected $casts = ['files' => 'collection'];
    };
});

it('storage methods', function (): void {
    expect($this->field)
        ->getDir()
        ->toBe('files')
        ->getDisk()
        ->toBe('public');
});

it('storage methods with slashes', function (): void {
    expect($this->field->dir('/files/'))
        ->getDir()
        ->toBe('files');
});

it('can be multiple methods', function (): void {
    expect($this->field)
        ->isMultiple()
        ->toBeFalse()
        ->and($this->fieldMultiple)
        ->isMultiple()
        ->toBeTrue();
});

it('removable methods', function (): void {
    expect($this->field)
        ->isRemovable()
        ->toBeFalse()
        ->and($this->field->removable())
        ->isRemovable()
        ->toBeTrue();
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('file');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.file');
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Fileable::class)
        ->toBeInstanceOf(RemovableContract::class);
});

it('accept attribute', function (): void {
    $this->field->accept('png');

    expect($this->field->getAttribute('accept'))
        ->toBe('png');
});

it('allowed extensions', function (): void {
    $this->field->allowedExtensions(['gif']);

    expect($this->field->getAllowedExtensions())
        ->toBe(['gif'])
        ->and($this->field->isAllowedExtension('gif'))
        ->toBeTrue()
        ->and($this->field->isAllowedExtension('png'))
        ->toBeFalse();
});

it('can download', function (): void {
    expect($this->field->canDownload())
        ->toBeTrue()
        ->and($this->field->disableDownload()->canDownload())
        ->toBeFalse();
});

it('correct path', function (): void {
    expect($this->field->path(''))
        ->toBe(Storage::disk($this->field->getDisk())->url(''))
        ->and($this->field->path('file.png'))
        ->toBe(Storage::disk($this->field->getDisk())->url('file.png'));
});

it('correct path with dir', function (): void {
    expect($this->field->pathWithDir(''))
        ->toBe(Storage::disk($this->field->getDisk())->url($this->field->getDir() . '/'))
        ->and($this->field->dir('')->pathWithDir('/'))
        ->toBe(Storage::disk($this->field->getDisk())->url('/'));
});

it('index view value', function (): void {
    expect($this->field->indexViewValue($this->item))
        ->toBe(view('moonshine::components.files', [
            'files' => [$this->field->pathWithDir($this->item->file)],
            'download' => $this->field->canDownload(),
        ])->render());
});

it('index view value for multiple', function (): void {
    $files = collect($this->item->files)
        ->map(fn ($value) => $this->fieldMultiple->pathWithDir($value))
        ->toArray();

    expect($this->fieldMultiple->indexViewValue($this->item))
        ->toBe(view('moonshine::components.files', [
            'files' => $files,
            'download' => $this->field->canDownload(),
        ])->render());
});

it('empty index view value', function (): void {
    $this->item->file = '';

    expect($this->field->indexViewValue($this->item))
        ->toBeEmpty();
});

it('empty index view value for multiple', function (): void {
    $this->item->files = '';

    expect($this->fieldMultiple->indexViewValue($this->item))
        ->toBeEmpty();
});

it('names single', function (): void {
    expect($this->field)
        ->name()
        ->toBe('file')
        ->name('1')
        ->toBe('file');
});

it('names multiple', function (): void {
    expect($this->fieldMultiple)
        ->name()
        ->toBe('files[]')
        ->name('1')
        ->toBe('files[1]');
});
