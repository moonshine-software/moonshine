<?php

namespace Leeto\MoonShine\Tests\Decorations;

use Leeto\MoonShine\Resources\MoonShineUserResource;
use PHPUnit\Framework\TestCase;

class BaseResourceTest extends TestCase
{
    public function testBasicResource()
    {
        $resource = new MoonShineUserResource();

        $this->assertEquals('moonshineusers', $resource->routeAlias());
    }
}