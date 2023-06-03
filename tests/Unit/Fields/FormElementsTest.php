<?php

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Exceptions\FieldsException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\HasMany;
use MoonShine\Fields\Number;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

it('only fields', function (): void {
    $fields = Fields::make([
        Tabs::make([
            Tab::make('Tab 1', exampleFields()->toArray()),
            Tab::make('Tab 2', [
                Heading::make('Heading'),
                Block::make(exampleFields()->toArray()),
            ]),
        ]),
    ]);

    expect($fields->onlyFields())
        ->toHaveCount(4)
        ->each
        ->toBeInstanceOf(Text::class);
});

it('with parents', function (): void {
    $fields = Fields::make([
        HasMany::make('Parent')
            ->fields(exampleFields()->toArray()),
    ]);

    expect($fields->withParents())
        ->first()
        ->getFields()
        ->each(fn ($field) => $field->parent()->toBeInstanceOf(HasMany::class));
});

it('when fields', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2')->showWhen('field1', ''),
    ]);

    expect($fields->whenFields())
        ->toHaveCount(1);
});

it('when fields names', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2')->showWhen('field1', ''),
    ]);

    expect($fields->whenFieldNames())
        ->toContain('field1')
        ->not->toContain('field2');
});

it('is when field', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2')->showWhen('field1', ''),
    ]);

    expect($fields->isWhenConditionField('field1'))
        ->toBeTrue();
});

it('extracted labels', function (): void {
    expect(exampleFields()->extractLabels())
        ->toBeArray()
        ->toBe(['field1' => 'Field 1', 'field2' => 'Field 2']);
});

it('find by resource class', function (): void {
    $resource = TestResourceBuilder::new();

    $fields = Fields::make([
        Text::make('Field 1'),
        HasMany::make('Has many', resource: $resource),
    ]);

    expect($fields->findByResourceClass($resource::class))
        ->toBeInstanceOf(HasMany::class);
});

it('find by relation', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        HasMany::make('Has many', 'relation'),
    ]);

    expect($fields->findByRelation('relation'))
        ->toBeInstanceOf(HasMany::class);
});

it('find by column', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        HasMany::make('Has many', 'column', 'relation'),
    ]);

    expect($fields->findByColumn('column'))
        ->toBeInstanceOf(HasMany::class);
});

it('only columns', function (): void {
    $fields = Fields::make([
        Text::make('Field 1'),
        HasMany::make('Has many', 'column', 'relation'),
    ]);

    expect($fields->onlyColumns()->toArray())
        ->toBe(['field1', 'column']);
});

it('transformed to columns by resource', function (): void {
    expect(TestResourceBuilder::buildWithFields()->getFields()->onlyColumns())
        ->toBeIterable()
        ->toContain('id', 'name')
        ->toBeInstanceOf(Fields::class)
        ->each->toBeScalar();
});

it('wrapped into decoration', function (): void {
    expect(exampleFields()->wrapIntoDecoration(Block::class, 'Label'))
        ->first()
        ->toBeInstanceOf(Block::class);
});

it('can`t be wrapped in a not decoration class', function (): void {
    exampleFields()->wrapIntoDecoration(Fields::class, 'Label');
})->throws(FieldsException::class, FieldsException::wrapError()->getMessage());

it('unwrap fields', function (): void {
    $fields = Fields::make([
        StackFields::make()->fields([
            Text::make('Text')
        ]),
        HasMany::make()->fields([
            Number::make('Text 2')
        ])
    ]);

    expect($fields->unwrapFields(StackFields::class)->first())
        ->toBeInstanceOf(Text::class)
        ->and($fields->unwrapFields(StackFields::class))
        ->each->not->toBeInstanceOf(StackFields::class);
});
