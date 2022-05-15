<?php

namespace Leeto\MoonShine\Tests\Resources;

use Leeto\MoonShine\Resources\MoonShineUserResource;
use PHPUnit\Framework\TestCase;

class BaseResourceTest extends TestCase
{
    public function testBasicResource()
    {
        $resource = new MoonShineUserResource();

        $this->assertEquals('moonshineusers', $resource->routeAlias());

        $this->assertNotEmpty($resource->getFields());
        $this->assertNotEmpty($resource->getFilters());
        $this->assertNotEmpty($resource->getActions());
    }
}