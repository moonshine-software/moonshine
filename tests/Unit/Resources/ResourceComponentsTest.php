<?php

use MoonShine\DetailComponents\DetailComponent;
use MoonShine\FormComponents\FormComponent;
use MoonShine\Resources\ResourceComponents;
use MoonShine\Tests\Fixtures\DetailComponents\TestDetailComponent;
use MoonShine\Tests\Fixtures\FormComponents\TestFormComponent;

uses()->group('resource-components');

beforeEach(function () {
    $this->collection = ResourceComponents::make([
        TestFormComponent::make('Form component'),
        TestDetailComponent::make('Detail component'),
    ]);
});

it('only form components', function () {
    expect($this->collection)
        ->count()
        ->toBe(2)
        ->and($this->collection->formComponents())
        ->count()
        ->toBe(1)
        ->and($this->collection->formComponents())
        ->toContainOnlyInstancesOf(FormComponent::class);
});

it('only detail components', function () {
    expect($this->collection)
        ->count()
        ->toBe(2)
        ->and($this->collection->detailComponents())
        ->count()
        ->toBe(1)
        ->and($this->collection->detailComponents())
        ->toContainOnlyInstancesOf(DetailComponent::class);
});
