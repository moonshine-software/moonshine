<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Views\CrudFormView;
use Leeto\MoonShine\Views\CrudIndexView;

class ViewControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_crud_index_view_response(): void
    {
        $endpoint = MoonShineRouter::to('view', [
            'resourceUri' => $this->testResource()->uriKey(),
            'viewUri' => MoonShineRouter::uriKey(CrudIndexView::class)
        ]);

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->get($endpoint);

        $response->assertOk();

        $response->assertJsonPath('endpoint', $endpoint);
    }

    /**
     * @test
     * @return void
     */
    public function it_crud_form_view_response(): void
    {
        $endpoint = MoonShineRouter::to('view.entity', [
            'resourceUri' => $this->testResource()->uriKey(),
            'viewUri' => MoonShineRouter::uriKey(CrudFormView::class),
        ]);

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->get($endpoint);

        $response->assertOk();

        $response->assertJsonPath('endpoint', $endpoint);
    }

    /**
     * @test
     * @return void
     */
    public function it_crud_form_entity_view_response(): void
    {
        $endpoint = MoonShineRouter::to('view.entity', [
            'resourceUri' => $this->testResource()->uriKey(),
            'viewUri' => MoonShineRouter::uriKey(CrudFormView::class),
            'id' => $this->adminUser()->getKey()
        ]);

        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->get($endpoint);

        $response->assertOk();

        $response->assertJsonPath('endpoint', $endpoint);
    }
}
