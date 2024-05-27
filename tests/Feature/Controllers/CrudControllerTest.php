<?php

declare(strict_types=1);

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Factories\CategoryFactory;
use MoonShine\Tests\Fixtures\Factories\CoverFactory;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\UI\Components\When;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Range;

uses()->group('crud-controller');

beforeEach(function (): void {
    $this->itemResource = addFieldsToTestResource([
        Range::make('Points')
            ->fromTo('start_point', 'end_point')
            ->withoutWrapper()
        ,

        DateRange::make('Dates')
            ->fromTo('start_date', 'end_date'),

        File::make('File title', 'file')->dir('items'),

        File::make('Files title', 'files')->dir('items')->multiple()->removable(),
    ]);

    CategoryFactory::new()
        ->has(
            CoverFactory::new()->count(1)
        )
        ->count(3)
        ->create();

    $this->storeData = [
        'name' => 'Test name storeData',
        'content' => 'Test content',
        'category_id' => Category::query()->inRandomOrder()->first(),
        'points' => [
            'start_point' => 50,
            'end_point' => 90,
        ],
        'dates' => [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
        ],
    ];

});

describe('without special fields', function () {
    it('crud store', function () {

        $date = date('Y-m-d', strtotime('-1 DAY'));

        $data = $this->storeData;
        $data['dates']['start_date'] = $date;
        $data['dates']['end_date'] = $date;

        $item = storeResource($this->itemResource, $data);

        expect($item->name)
            ->toBe('Test name storeData')
            ->and($item->content)
            ->toBe('Test content')
            ->and($item->start_point)
            ->toBe(50)
            ->and($item->end_point)
            ->toBe(90)
            ->and($item->start_date)
            ->toBe($date)
            ->and($item->end_date)
            ->toBe($date)
        ;
    });

    it('crud update', function () {
        $item = storeResource($this->itemResource, $this->storeData);

        asAdmin()->put(
            $this->itemResource->route('crud.update', $item->getKey()),
            ['name' => 'New test name']
        )
            ->assertRedirect();

        $item->refresh();

        expect($item->name)
            ->toBe('New test name');
    });

    it('crud delete', function () {
        $item = storeResource($this->itemResource, $this->storeData);

        asAdmin()->delete(
            $this->itemResource->route('crud.destroy', $item->getKey())
        )
            ->assertRedirect();
    });

    it('when component crud delete', function () {

        $item = storeResource($this->itemResource, $this->storeData);

        $resource = new TestResource();

        fakeRequest($resource->route('crud.destroy', $item->getKey()), 'DELETE', dispatchRoute: true);

        TestResourceBuilder::new(Item::class)
            ->setTestFormFields([
                When::make(
                    fn () => is_null($item?->getKey()),
                    fn () => [
                        DateRange::make('Range')->fromTo('start_date', 'end_date'),
                    ]
                ),
            ])
        ;
    });

    it('crud delete item with null cast value', function () {
        $item = createItem(1, 0);

        $item->files = null;
        $item->save();

        asAdmin()->delete(
            $this->itemResource->route('crud.destroy', $item->getKey())
        )
            ->assertRedirect();
    });

    it('crud mass delete', function () {
        createItem(3);

        $ids = Item::query()->get()->pluck('id')->toArray();

        asAdmin()->delete(
            $this->itemResource->route('crud.massDelete', query: ['ids' => $ids])
        )
            ->assertRedirect();

        $items = Item::query()->get()->toArray();

        expect($items)->toBeEmpty();
    });
});


function storeResource(ModelResource $resource, $saveData)
{
    asAdmin()->post(
        $resource->route('crud.store'),
        $saveData
    )
        ->assertRedirect();

    return Item::query()->where('name', 'Test name storeData')->first();
}
