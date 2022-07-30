<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Menu;

use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;
use Leeto\MoonShine\Tests\TestCase;

class MenuTest extends TestCase
{
    public function test_register_menu_item()
    {
        $this->assertNotEmpty(app(Menu::class)->all());
        $this->assertCount(1, app(Menu::class)->all());

        foreach (app(Menu::class)->all() as $item) {
            $this->assertEquals($item->resource()->title(), $item->title());
        }
    }

    public function test_register_menu_group()
    {
        app(MoonShine::class)->registerResources([
            MenuGroup::make('Section 1', [
                MenuItem::make('Section inner', MoonShineUserRoleResource::class)
            ])
        ]);

        $this->assertNotEmpty(app(Menu::class)->all());
        $this->assertCount(1, app(Menu::class)->all());

        foreach (app(Menu::class)->all() as $item) {
            $this->assertTrue($item->isGroup());
            $this->assertEquals('Section 1', $item->title());
            $this->assertNotEmpty($item->items());

            foreach ($item->items() as $inner) {
                $this->assertTrue(!$inner->isGroup());
                $this->assertEquals('Section inner', $inner->title());
                $this->assertInstanceOf(MoonShineUserRoleResource::class, $inner->resource());
            }
        }
    }
}
