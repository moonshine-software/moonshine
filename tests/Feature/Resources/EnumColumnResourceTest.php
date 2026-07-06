<?php

declare(strict_types=1);

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Tests\Fixtures\Enums\TestEnumLabeled;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestEnumColumnResource;

uses()->group('resources-feature');

beforeEach(function (): void {
    $this->resource = app(TestEnumColumnResource::class);

    $this->item = Item::factory()->create([
        'status' => TestEnumLabeled::Web,
    ]);
});

it('renders the detail page when the display column is an enum cast', function (): void {
    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: DetailPage::class,
            resource: $this->resource,
            params: ['resourceItem' => $this->item->getKey()],
        )
    )
        ->assertOk()
        ->assertSee('web-platform');
});

it('renders the form page when the display column is an enum cast', function (): void {
    asAdmin()->get(
        $this->moonshineCore->getRouter()->getEndpoints()->toPage(
            page: FormPage::class,
            resource: $this->resource,
            params: ['resourceItem' => $this->item->getKey()],
        )
    )
        ->assertOk()
        ->assertSee('web-platform');
});
