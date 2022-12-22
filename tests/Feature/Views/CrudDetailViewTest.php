<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Views;

use Leeto\MoonShine\Contracts\EntityContract;
use Leeto\MoonShine\Exceptions\ViewComponentException;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\ViewComponents\DetailCard\DetailCard;
use Leeto\MoonShine\ViewComponents\ViewComponents;
use Leeto\MoonShine\Views\CrudDetailView;

final class CrudDetailViewTest extends TestCase
{
    protected EntityContract $value;
    protected CrudDetailView $view;

    protected function setUp(): void
    {
        parent::setUp();

        $this->value = $this->testResource()->entity($this->adminUser());
        $this->view = CrudDetailView::make($this->testResource(), $this->value);
    }

    /**
     * @test
     * @return void
     */
    public function it_make(): void
    {
        $this->assertInstanceOf(CrudDetailView::class, $this->view);
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
            MoonShineRouter::to('view.entity', [
                'resourceUri' => $this->testResource()->uriKey(),
                'viewUri' => $this->view->uriKey(),
                'id' => $this->value->id(),
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
    public function it_resolve_detail_card_component(): void
    {
        $this->assertInstanceOf(DetailCard::class, $this->view->resolveComponent(DetailCard::class));
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
