<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Menu;

use Leeto\MoonShine\Exceptions\MenuException;
use Leeto\MoonShine\Menu\Menu;
use Leeto\MoonShine\Menu\MenuGroup;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;
use Leeto\MoonShine\Tests\TestCase;

class MenuTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws MenuException
     */
    public function it_register_menu_item(): void
    {
        app(MoonShine::class)->menu([
            MenuItem::make($this->testResource())
        ]);

        $this->assertNotEmpty(app(Menu::class)->all());
        $this->assertCount(1, app(Menu::class)->all());

        foreach (app(Menu::class)->all() as $item) {
            $this->assertEquals($item->resource()->title(), $item->title());
        }
    }

    /**
     * @test
     * @return void
     * @throws MenuException
     */
    public function it_register_menu_group(): void
    {
        app(MoonShine::class)->menu([
            MenuGroup::make('Section 1', [
                MenuItem::make(new MoonShineUserRoleResource(), 'Section inner')
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

    /**
     * @test
     * @return void
     */
    public function it_not_allowed_menu_type(): void
    {
        $this->expectException(MenuException::class);
        $this->expectDeprecationMessage(MenuException::onlyMenuItemAllowed()->getMessage());

        app(MoonShine::class)->menu([
            new MoonShineUserRoleResource()
        ]);
    }

}
