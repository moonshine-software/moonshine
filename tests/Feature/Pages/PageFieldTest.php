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

    MoonshineUser::factory()->create([
        'id' => 12,
        'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
        'name' => 'Test Name',
        'email' => 'test@mail.ru',
        'password' => bcrypt('test'),
    ]);

    $this->resource = TestResourceBuilder::testResourceWithAllFeatures();
});

it('fields on index', function () {
    asAdmin()->get(
        to_page($this->resource, IndexPage::class)
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

it('fields on form', function () {
    asAdmin()->get(
        to_page($this->resource, FormPage::class, ['resourceItem' => 12])
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
        to_page($this->resource, DetailPage::class, ['resourceItem' => 12])
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
