<?php

use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Tabs\Tabs;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\StackFields;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

uses()->group('fields');
uses()->group('core');

beforeEach(function () {
    $this->data = Box::make([
        FormBuilder::make()->fields([
            Tabs::make([
                Tab::make([
                    LineBreak::make()->name('line-break'),
                    FormBuilder::make()->name('inner-form')->fields([
                        Switcher::make('Switcher'),
                        StackFields::make()->fields([
                            Text::make('Text')->sortable(),
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
            fields: $this->collection
                ->withoutOutside()
                ->onlyFields()
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
