<?php

declare(strict_types=1);

use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('pages-feature');

beforeEach(function (): void {

    MoonShineUser::factory()->count(10)->create();

    $this->userID = MoonshineUser::query()->max('id') + 1;

    MoonshineUser::factory()->create([
        'id' => $this->userID,
        'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
        'name' => 'Test Name',
        'email' => 'test@mail.ru',
        'password' => bcrypt('test'),
    ]);

    $this->resource = TestResourceBuilder::testResourceWithAllFeatures();
});

it('fields on index', function () {
    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('table')
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Badge')
        ->assertSee('badge-red')
        ->assertSee('Item #1 Query Tag')
        ->assertSee('Test button')
        ->assertSee('TestValueMetric')
        ->assertSee('TestLineChartMetric')
        ->assertSee('TestDonutChartMetric')
        ->assertSee('data-test-td-attr')
        ->assertSee('data-test-tr-attr')
    ;
});

it('simple pagination on index', function () {
    $this->resource->setSimplePaginate();

    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $this->resource)
    )
        ->assertOk()
        ->assertSee('table')
        ->assertSee('Next')
        ->assertSee('Previous')
    ;
});

it('fields on form', function () {
    asAdmin()->get(
        to_page(page: FormPage::class, resource: $this->resource, params: ['resourceItem' => $this->userID])
    )
        ->assertOk()
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Password')
        ->assertSee('Test Name')
        ->assertSee('test@mail.ru')
    ;
});

it('fields on show', function () {
    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $this->resource, params: ['resourceItem' => $this->userID])
    )
        ->assertOk()
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Password')
        ->assertSee('Badge')
        ->assertSee('red')
        ->assertSee('Test Name')
        ->assertSee('test@mail.ru')
    ;
});
