<?php

use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\BelongsToMany;
use MoonShine\Fields\Fields;
use MoonShine\Fields\File;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Json;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

it('resolved child fields / names', function () {
    $parent = HasMany::make('Has many');

    expect(exampleFields()->resolveChildFields($parent))
        ->toBeIterable()
        ->each(
            fn ($field, $index) => $field->name()->toBe("hasMany[field".($index + 1)."]")
        );
});

it('resolved child fields / json with exception ', function () {
    $json = Json::make('Parent');

    $relationFields = Fields::make([
        HasMany::make('Child'),
    ]);

    $relationFields->resolveChildFields($json);
})->throws(FieldException::class);

it('resolved child fields / pivot', function () {
    $field = BelongsToMany::make('Field')
        ->fields(exampleFields()->toArray());

    expect(exampleFields()->resolveChildFields($field))
        ->toBeIterable()
        ->each(
            fn ($field, $index) => $field->name()->toBe("field_field".($index + 1)."[]")
        );
});

it('resolved child fields / child is related field on form', function () {
    $this->get(test()->moonShineUserResource()->route('create'));

    $parent = HasMany::make('Parent');

    $fields = Fields::make([
        HasMany::make('Child'),
    ]);

    expect($fields->resolveChildFields($parent))
        ->toBeIterable()
        ->each->toBeInstanceOf(NoInput::class);
});

it('resolved child fields / multiple', function () {
    $parent = HasMany::make('Parent');

    $fields = Fields::make([
        Select::make('Child')
            ->multiple(),
    ]);

    expect($fields->resolveChildFields($parent))
        ->toBeIterable()
        ->each(
            fn ($field, $index) => $field->name()->toBe("parent[child][]")
        );
});

it('resolved child fields / x-model', function () {
    $parent = HasMany::make('Parent')->fields(
        exampleFields()->toArray()
    );

    $fields = Fields::make([
        Select::make('Child')
            ->multiple(),
    ]);

    expect($fields->resolveChildFields($parent))
        ->toBeIterable()
        ->each(
            fn ($field, $index) => $field->name()->toBe("parent[\${index0}][child][]")
        );
});

it('index fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1')->hideOnIndex(),
            Text::make('Field 2')->showOnIndex(),
        ])->indexFields()
    )->toHaveCount(1)
        ->each(fn ($field) => $field->field()->toBe('field2'));
});

it('relatable fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1'),
            HasMany::make('Field 2', resource: TestResourceBuilder::new())->resourceMode(),
        ])->relatable()
    )->toHaveCount(1)->each->toBeInstanceOf(HasMany::class);
});

it('without can be relatable fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1'),
            HasMany::make('Field 2'),
        ])->withoutCanBeRelatable()
    )->toHaveCount(1)->each->toBeInstanceOf(Text::class);
});

it('without relatable fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1'),
            HasMany::make('Field 2', resource: TestResourceBuilder::new())->resourceMode(),
        ])->withoutRelatable()
    )->toHaveCount(1)->each->toBeInstanceOf(Text::class);
});

it('only form fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1')->hideOnForm(),
            Text::make('Field 2')->showOnForm(),
        ])->formFields()
    )->toHaveCount(1)
        ->each(fn ($field) => $field->field()->toBe('field2'));
});

it('only detail fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1')->hideOnDetail(),
            Text::make('Field 2')->showOnDetail(),
        ])->detailFields()
    )->toHaveCount(1)
        ->each(fn ($field) => $field->field()->toBe('field2'));
});

it('only export fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1')->hideOnExport(),
            Text::make('Field 2')->showOnExport(),
        ])->exportFields()
    )->toHaveCount(1)
        ->each(fn ($field) => $field->field()->toBe('field2'));
});

it('only import fields', function () {
    expect(
        Fields::make([
            Text::make('Field 1')->useOnImport(false),
            Text::make('Field 2')->useOnImport(true),
        ])->importFields()
    )->toHaveCount(1)
        ->each(fn ($field) => $field->field()->toBe('field2'));
});

it('only file fields', function () {
    expect(
        Fields::make([
            File::make('File 1'),
            File::make('File 2'),
            Text::make('Text'),
        ])->onlyFileFields()
    )->toHaveCount(2)->each->toBeInstanceOf(File::class);
});

it('only file fields with isDeleteFiles true', function () {
    expect(
        Fields::make([
            File::make('File 1'),
            File::make('File 2')->disableDeleteFiles(),
            Text::make('Text'),
        ])->onlyDeletableFileFields()
    )->toHaveCount(1)->each->toBeInstanceOf(File::class);
});
