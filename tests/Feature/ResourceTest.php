<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Tests\TestCase;

class ResourceTest extends TestCase
{
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new MoonShineUserResource();
    }

    public function test_get_model()
    {
        $this->assertInstanceOf(MoonshineUser::class, $this->resource->getModel());
    }

    public function test_uri_key()
    {
        $this->assertEquals('moon-shine-user-resource', $this->resource->uriKey());
    }
}
