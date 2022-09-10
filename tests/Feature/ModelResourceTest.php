<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Tests\TestCase;

class ModelResourceTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_get_model(): void
    {
        $this->assertInstanceOf(
            MoonshineUser::class,
            $this->testResource()->getModel()
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_uri_key(): void
    {
        $this->assertEquals(
            'moon-shine-user-resource',
            $this->testResource()->uriKey()
        );
    }
}
