<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Unit;

use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Views\CrudDetailView;
use Leeto\MoonShine\Views\CrudFormView;
use Leeto\MoonShine\Views\CrudIndexView;
use Leeto\MoonShine\Views\Views;

class ViewsTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_find_by_uri(): void
    {
        $views = Views::make([
            CrudIndexView::class,
            CrudFormView::class,
            CrudDetailView::class
        ]);

        $view = $views->findByUriKey('crud-index-view');

        $this->assertEquals(CrudIndexView::class, $view);
    }

    /**
     * @test
     * @return void
     */
    public function it_find_by_uri_default(): void
    {
        $views = Views::make([
            CrudIndexView::class,
            CrudFormView::class,
            CrudDetailView::class
        ]);

        $view = $views->findByUriKey('test', CrudIndexView::class);

        $this->assertEquals(CrudIndexView::class, $view);
    }

    /**
     * @test
     * @return void
     */
    public function it_find_by_uri_null(): void
    {
        $views = Views::make([
            CrudIndexView::class,
            CrudFormView::class,
            CrudDetailView::class
        ]);

        $view = $views->findByUriKey('test');

        $this->assertNull($view);
    }
}
