<?php

use MoonShine\DetailComponents\DetailComponent;
use MoonShine\FormComponents\FormComponent;
use MoonShine\IndexComponents\IndexComponent;
use MoonShine\Resources\ResourceComponents;
use MoonShine\Tests\Fixtures\DetailComponents\TestDetailComponent;
use MoonShine\Tests\Fixtures\FormComponents\TestFormComponent;
use MoonShine\Tests\Fixtures\IndexComponents\TestIndexComponent;

uses()->group('resource-components');

beforeEach(function (): void {
    $this->collection = ResourceComponents::make([
        TestFormComponent::make('Form component'),
        TestDetailComponent::make('Detail component'),
        TestIndexComponent::make('Index component'),
    ]);
});

it('only form components', function (): void {
    expect($this->collection)
        ->count()
        ->toBe(3)
        ->and($this->collection->formComponents())
        ->count()
        ->toBe(1)
        ->and($this->collection->formComponents())
        ->toContainOnlyInstancesOf(FormComponent::class);
});

it('only detail components', function (): void {
    expect($this->collection)
        ->count()
        ->toBe(3)
        ->and($this->collection->detailComponents())
        ->count()
        ->toBe(1)
        ->and($this->collection->detailComponents())
        ->toContainOnlyInstancesOf(DetailComponent::class);
});

it('only index components', function (): void {
    expect($this->collection)
        ->count()
        ->toBe(3)
        ->and($this->collection->indexComponents())
        ->count()
        ->toBe(1)
        ->and($this->collection->indexComponents())
        ->toContainOnlyInstancesOf(IndexComponent::class);
});
