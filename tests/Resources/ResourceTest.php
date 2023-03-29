<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Resources;

use Leeto\MoonShine\Actions\Actions;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Filters\Filters;
use Leeto\MoonShine\Tests\TestCase;

class ResourceTest extends TestCase
{
    public function test_basic()
    {
        $resource = $this->testResource();

        $this->assertNotEmpty($resource->getFields());
        $this->assertNotEmpty($resource->getFilters());
        $this->assertNotEmpty($resource->getActions());

        $this->assertEquals(Fields::class, get_class($resource->getFields()));
        $this->assertEquals(Filters::class, get_class($resource->getFilters()));
        $this->assertEquals(Actions::class, get_class($resource->getActions()));
    }

    public function test_route()
    {
        $resource = $this->testResource();

        $this->assertEquals('moonshine.moonShineUsers', $resource->routeName());
        $this->assertEquals('moonShineUsers', $resource->routeNameAlias());

        $this->assertStringContainsString('/moonshine/resource/moon-shine-user-resource', $resource->route('index'));
        $this->assertStringContainsString('/moonshine/resource/moon-shine-user-resource/create', $resource->route('create'));
        $this->assertStringContainsString('/moonshine/resource/moon-shine-user-resource/1', $resource->route('show', 1));
        $this->assertStringContainsString('/moonshine/resource/moon-shine-user-resource/1/edit', $resource->route('edit', 1));
        $this->assertStringContainsString('/moonshine/resource/moon-shine-user-resource/1', $resource->route('destroy', 1));
    }
}
