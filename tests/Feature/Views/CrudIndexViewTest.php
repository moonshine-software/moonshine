<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Views;

use Leeto\MoonShine\Exceptions\ViewComponentException;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\ViewComponents\Table\Table;
use Leeto\MoonShine\ViewComponents\ViewComponents;
use Leeto\MoonShine\Views\CrudIndexView;

final class CrudIndexViewTest extends TestCase
{
    protected CrudIndexView $view;

    protected function setUp(): void
    {
        parent::setUp();

        $this->view = CrudIndexView::make($this->testResource());
    }

    /**
     * @test
     * @return void
     */
    public function it_make(): void
    {
        $this->assertInstanceOf(CrudIndexView::class, $this->view);
    }

    /**
     * @test
     * @return void
     */
    public function it_valid_resource(): void
    {
        $this->assertEquals($this->testResource(), $this->view->resource());
    }


    /**
     * @test
     * @return void
     */
    public function it_valid_endpoint(): void
    {
        $this->assertEquals(
            MoonShineRouter::to('view', [
                'resourceUri' => $this->testResource()->uriKey(),
                'viewUri' => $this->view->uriKey()
            ]),
            $this->view->endpoint()
        );
    }


    /**
     * @test
     * @return void
     */
    public function it_components(): void
    {
        $this->assertInstanceOf(ViewComponents::class, $this->view->components());
        $this->assertNotEmpty($this->view->components());
    }

    /**
     * @test
     * @return void
     * @throws ViewComponentException
     */
    public function it_resolve_table_component(): void
    {
        $this->assertInstanceOf(Table::class, $this->view->resolveComponent(Table::class));
    }

    public function it_not_found_component(): void
    {
        $this->expectException(ViewComponentException::class);
        $this->expectExceptionMessage(
            ViewComponentException::notFoundInView($this->view::class)
                ->getMessage()
        );

        $this->view->resolveComponent('Testing');
    }

}
