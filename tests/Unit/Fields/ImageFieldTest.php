<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\File;
use MoonShine\Fields\Image;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function (): void {
    $this->field = Image::make('Image')
        ->disk('public')
        ->dir('images');

    $this->fieldMultiple = Image::make('Images')
        ->multiple()
        ->disk('public')
        ->dir('images');

    $this->item = new class () extends Model {
        public string $image = 'images/image.png';
        public string $images = '["images/image1.png", "images/image2.png"]';

        protected $casts = ['images' => 'collection'];
    };

    $this->field->resolveFill(
        ['image' => 'images/image.png'],
        $this->item
    );

    $this->fieldMultiple->resolveFill(
        ['images' => ["images/image1.png", "images/image2.png"]],
        $this->item
    );
});


it('file is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(File::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('file');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.image');
});

it('preview', function (): void {
    expect((string)$this->field)
        ->toBe(
            view('moonshine::fields.image', [
                'element' => $this->field,
            ])->render()
        );
});

it('preview for multiple', function (): void {
    expect((string)$this->fieldMultiple)
        ->toBe(
            view('moonshine::fields.image', [
                'element' => $this->fieldMultiple,
            ])->render()
        );
});
