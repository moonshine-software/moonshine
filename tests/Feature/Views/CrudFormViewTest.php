<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Views;

use Leeto\MoonShine\Contracts\ValueEntityContract;
use Leeto\MoonShine\Exceptions\ViewComponentException;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\ViewComponents\Form\Form;
use Leeto\MoonShine\ViewComponents\ViewComponents;
use Leeto\MoonShine\Views\CrudFormView;

final class CrudFormViewTest extends TestCase
{
    protected ValueEntityContract $value;
    protected CrudFormView $view;

    protected function setUp(): void
    {
        parent::setUp();

        $this->value = $this->testResource()->valueEntity($this->adminUser());
        $this->view = CrudFormView::make($this->testResource(), $this->value);
    }

    /**
     * @test
     * @return void
     */
    public function it_make(): void
    {
        $this->assertInstanceOf(CrudFormView::class, $this->view);
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
    public function it_resolve_form_component(): void
    {
        $this->assertInstanceOf(Form::class, $this->view->resolveComponent(Form::class));
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
