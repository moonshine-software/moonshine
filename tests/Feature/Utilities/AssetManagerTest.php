<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Utilities;

use Leeto\MoonShine\Fields\Code;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Utilities\AssetManager;

class AssetManagerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_empty(): void
    {
        $this->assertEmpty(app(AssetManager::class)->css());
        $this->assertEmpty(app(AssetManager::class)->js());
    }

    /**
     * @test
     * @return void
     */
    public function it_add(): void
    {
        app(AssetManager::class)->add('link1');
        app(AssetManager::class)->add('link4');
        app(AssetManager::class)->add(['link1', 'link2', 'link3']);

        $this->assertCount(4, app(AssetManager::class)->getAssets());
    }

    /**
     * @test
     * @return void
     */
    public function it_js(): void
    {
        app(AssetManager::class)->add('link1.js');

        $this->assertStringContainsString(asset('link1.js'), app(AssetManager::class)->js());
    }

    /**
     * @test
     * @return void
     */
    public function it_css(): void
    {
        app(AssetManager::class)->add('link1.css');

        $this->assertStringContainsString(asset('link1.css'), app(AssetManager::class)->css());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_assets(): void
    {
        $field = Code::make('Test');

        $this->assertEquals($field->getAssets(), app(AssetManager::class)->getAssets());
    }
}
