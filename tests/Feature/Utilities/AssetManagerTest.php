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
        $this->assertEmpty(AssetManager::css());
        $this->assertEmpty(AssetManager::js());
    }

    /**
     * @test
     * @return void
     */
    public function it_add(): void
    {
        AssetManager::clear();

        AssetManager::add('link1');
        AssetManager::add('link4');
        AssetManager::add(['link1', 'link2', 'link3']);

        $this->assertCount(4, AssetManager::getAssets());
    }

    /**
     * @test
     * @return void
     */
    public function it_js(): void
    {
        AssetManager::clear();
        AssetManager::add('link1.js');

        $this->assertStringContainsString(
            asset('link1.js'),
            AssetManager::js()
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_css(): void
    {
        AssetManager::clear();
        AssetManager::add('link1.css');

        $this->assertStringContainsString(
            asset('link1.css'),
            AssetManager::css()
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_field_assets(): void
    {
        AssetManager::clear();

        $field = Code::make('Test');

        $this->assertEquals(
            $field->getAssets(),
            AssetManager::getAssets()
        );
    }
}
