<?php

declare(strict_types=1);

use MoonShine\Tests\Fixtures\Enums\TestEnumBreadcrumbLabel;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\UI\Components\Breadcrumbs;

uses()->group('pages');

it('renders the resource column in page breadcrumbs', function (
    string $pageMethod,
    string|int|null $value,
    ?string $enumClass,
    string $expected,
) {
    $item = new Item();

    if ($enumClass !== null) {
        $item->mergeCasts(['name' => $enumClass]);
    }

    $item->setRawAttributes(['id' => 1, 'name' => $value]);

    $resource = app(TestItemResource::class)->setItem($item)->setItemID(1);
    $page = $resource->{$pageMethod}();
    $breadcrumbs = $page->getBreadcrumbs();

    expect($breadcrumbs[$page->getRoute()])->toBe($expected);

    $html = (string) Breadcrumbs::make($breadcrumbs)->render();

    expect(preg_replace('/\s+/', ' ', trim(strip_tags($html))))->toBe(trim('Items ' . $expected));
})->with([
    'form page' => 'getFormPage',
    'detail page' => 'getDetailPage',
])->with([
    'backed enum' => ['R', TestEnumColor::class, 'R'],
    'enum with toString' => ['blue', TestEnumBreadcrumbLabel::class, 'Blue label'],
    'string' => ['Article title', null, 'Article title'],
    'zero' => [0, null, '0'],
    'null' => [null, null, ''],
]);
