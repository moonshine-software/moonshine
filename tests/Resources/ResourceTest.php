<?php

namespace Leeto\MoonShine\Tests\Resources;

use Leeto\MoonShine\Http\Controllers\MoonShineUserController;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Tests\TestCase;

class ResourceTest extends TestCase
{
    public function test_basic()
    {
        $resource = new MoonShineUserResource();

        $this->assertNotEmpty($resource->getFields());
        $this->assertNotEmpty($resource->getFilters());
        $this->assertNotEmpty($resource->getActions());
    }

    public function test_route()
    {
        app(MoonShine::class)->registerResources([
            MoonShineUserResource::class
        ]);

        $resource = new MoonShineUserResource();

        $this->assertEquals('moonshine.moonShineUsers', $resource->routeName());
        $this->assertEquals('moonShineUsers', $resource->routeAlias());
        $this->assertEquals(MoonShineUserController::class, $resource->controllerName());

        $this->assertStringContainsString('/moonshine/moonShineUsers', $resource->route('index'));
        $this->assertStringContainsString('/moonshine/moonShineUsers/create', $resource->route('create'));
        $this->assertStringContainsString('/moonshine/moonShineUsers/1/edit', $resource->route('edit', 1));
        $this->assertStringContainsString('/moonshine/moonShineUsers/1', $resource->route('destroy', 1));
    }
}