<?php

use MoonShine\Components\FormBuilder;
use MoonShine\Components\Layout\Box;
use MoonShine\Components\Layout\LineBreak;
use MoonShine\Components\TableBuilder;
use MoonShine\Components\Tabs\Tab;
use MoonShine\Components\Tabs\Tabs;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Fields\StackFields;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Tests\Fixtures\Resources\TestResource;

uses()->group('fields');
uses()->group('core');

beforeEach(function () {
    $this->data = Box::make([
        FormBuilder::make()->fields([
            Tabs::make([
                Tab::make([
                    LineBreak::make()->name('line-break'),
                    FormBuilder::make()->name('inner-form')->fields([
                        Switcher::make('Switcher')->useOnImport(),
                        StackFields::make()->fields([
                            Text::make('Text')->hideOnForm()->sortable(),
                            Text::make('Email')->showWhen('column', '=', 'value'),
                        ]),
                        HasOne::make('HasOne', 'hasone', resource: new TestResource()),
                    ]),

                ]),
                Tab::make([
                    TableBuilder::make()
                        ->name('first-table'),

                    TableBuilder::make()
                        ->name('second-table'),
                ]),
            ]),
        ])->name('parent-form'),

        TableBuilder::make()
            ->name('parent-table'),
    ])->name('block');

    $this->collection = Fields::make([
        $this->data,
    ]);
});

describe('form elements', function () {
    it('only fields', function () {
        expect($this->collection->onlyFields())
            ->toHaveCount(4)
            ->each->toBeInstanceOf(Field::class);
    });

    it('only fields with wrappers', function () {
        expect($this->collection->onlyFields(withWrappers: true))
            ->toHaveCount(3)
            ->each->toBeInstanceOf(Field::class);
    });

    it('prepare attributes', function () {
        expect($this->collection->prepareAttributes())
            ->toHaveCount(4)
            ->each(fn ($expect) => $expect->getAttribute('x-on:change')->toContain('onChangeField($event)'))
        ;
    });

    it('show when', function () {
        $form = FormBuilder::make(
            '/',
            fields: $this->collection->onlyFields()
        );

        expect(data_get($form->render()->getData(), 'attributes')->get('x-init'))
            ->toContain('whenFields', 'column', '=', 'value')
        ;
    });
});

describe('fields', function () {
    it('fill cloned', function () {
        $values = [
            'switcher' => 'value',
            'text' => 'value',
            'email' => 'value',
        ];

        expect($this->collection->fillCloned($values)->withoutOutside())
            ->each(fn ($expect) => $expect->toValue()->toBe('value'))
        ;
    });

    it('fill', function () {
        $values = [
            'switcher' => 'value',
            'text' => 'value',
            'email' => 'value',
        ];

        $this->collection->fill($values);

        expect($this->collection->onlyFields()->withoutOutside())
            ->each(fn ($expect) => $expect->toValue()->toBe('value'))
        ;
    });

    it('wrap names', function () {
        $this->collection->wrapNames('filters');

        expect($this->collection->onlyFields())
            ->each(fn ($expect) => $expect->getNameAttribute()->toContain('filters'))
        ;
    });

    it('reset', function () {
        $values = [
            'switcher' => 'value',
            'text' => 'value',
            'email' => 'value',
        ];

        $this->collection->fill($values);
        $this->collection->reset();

        expect($this->collection->onlyFields()->withoutOutside())
            ->each(fn ($expect) => $expect->toValue()->toBe(null))
        ;
    });

    it('without has fields', function () {
        expect($this->collection->onlyFields(withWrappers: true)->withoutHasFields())
            ->toHaveCount(1)
            ->each->not->toBeInstanceOf(HasFields::class)
        ;
    });

    it('only has fields', function () {
        expect($this->collection->onlyFields(withWrappers: true)->onlyHasFields())
            ->toHaveCount(2)
            ->each->toBeInstanceOf(HasFields::class)
        ;
    });

    it('without outside', function () {
        expect($this->collection->onlyFields()->withoutOutside())
            ->toHaveCount(3)
            ->each->not->toBeInstanceOf(ModelRelationField::class)
        ;
    });

    it('only outside', function () {
        expect($this->collection->onlyFields()->onlyOutside())
            ->toHaveCount(1)
            ->each->toBeInstanceOf(HasOne::class)
        ;
    });

    it('without relation fields', function () {
        expect($this->collection->onlyFields()->withoutRelationFields())
            ->toHaveCount(3)
            ->each->not->toBeInstanceOf(ModelRelationField::class)
        ;
    });

    it('only relation fields', function () {
        expect($this->collection->onlyFields()->onlyRelationFields())
            ->toHaveCount(1)
            ->each->toBeInstanceOf(HasOne::class)
        ;
    });

    it('index fields', function () {
        expect($this->collection->onlyFields()->indexFields())
            ->each(fn ($expect) => $expect->isOnIndex()->toBeTrue())
        ;
    });

    it('form fields', function () {
        expect($this->collection->onlyFields()->formFields())
            ->each(fn ($expect) => $expect->isOnForm()->toBeTrue())
        ;
    });

    it('detail fields', function () {
        expect($this->collection->onlyFields()->detailFields())
            ->each(fn ($expect) => $expect->isOnDetail()->toBeTrue())
        ;
    });

    it('export fields', function () {
        expect($this->collection->onlyFields()->exportFields())
            ->each(fn ($expect) => $expect->isOnExport()->toBeTrue())
        ;
    });

    it('import fields', function () {
        expect($this->collection->onlyFields()->importFields())
            ->each(fn ($expect) => $expect->isOnImport()->toBeTrue())
        ;
    });

    it('extract labels', function () {
        expect($this->collection->onlyFields()->extractLabels())
            ->toBe([
                'switcher' => 'Switcher',
                'text' => 'Text',
                'email' => 'Email',
                'hasone' => 'HasOne',
            ])
        ;
    });

    it('find by relation', function () {
        expect($this->collection->onlyFields()->findByRelation('hasone'))
            ->toBeInstanceOf(HasOne::class)
        ;
    });

    it('find by column', function () {
        expect($this->collection->onlyFields()->findByColumn('hasone'))
            ->toBeInstanceOf(HasOne::class)
        ;
    });

    it('find by class', function () {
        expect($this->collection->onlyFields()->findByClass(HasOne::class))
            ->toBeInstanceOf(HasOne::class)
        ;
    });
});
