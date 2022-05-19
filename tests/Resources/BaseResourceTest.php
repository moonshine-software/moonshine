<?php

namespace Leeto\MoonShine\Tests\Resources;

use Leeto\MoonShine\Controllers\MoonShineUserController;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Tests\TestCase;

class BaseResourceTest extends TestCase
{
    public function testBasic()
    {
        $resource = new MoonShineUserResource();

        $this->assertNotEmpty($resource->getFields());
        $this->assertNotEmpty($resource->getFilters());
        $this->assertNotEmpty($resource->getActions());
    }

    public function testRoute()
    {
        app(MoonShine::class)->registerResources([
            MoonShineUserResource::class
        ]);

        $resource = new MoonShineUserResource();

        $this->assertEquals('moonshine.moonshineusers', $resource->routeName());
        $this->assertEquals('moonshineusers', $resource->routeAlias());
        $this->assertEquals(MoonShineUserController::class, $resource->controllerName());

        $this->assertStringContainsString('/moonshine/moonshineusers', $resource->route('index'));
        $this->assertStringContainsString('/moonshine/moonshineusers/create', $resource->route('create'));
        $this->assertStringContainsString('/moonshine/moonshineusers/1/edit', $resource->route('edit', 1));
        $this->assertStringContainsString('/moonshine/moonshineusers/1', $resource->route('destroy', 1));
    }
}