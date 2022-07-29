<?php

namespace Leeto\MoonShine\Tests\Resources;

use Leeto\MoonShine\Fields\Code;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Utilities\AssetManager;

class AssetManagerTest extends TestCase
{
    public function test_empty()
    {
        $this->assertEmpty(app(AssetManager::class)->css());
        $this->assertEmpty(app(AssetManager::class)->js());
    }

    public function test_add()
    {
        app(AssetManager::class)->add('link1');
        app(AssetManager::class)->add('link4');
        app(AssetManager::class)->add(['link1', 'link2', 'link3']);

        $this->assertCount(4, app(AssetManager::class)->getAssets());
    }

    public function test_js()
    {
        app(AssetManager::class)->add('link1.js');

        $this->assertStringContainsString(asset('link1.js'), app(AssetManager::class)->js());
    }

    public function test_css()
    {
        app(AssetManager::class)->add('link1.css');

        $this->assertStringContainsString(asset('link1.css'), app(AssetManager::class)->css());
    }

    public function test_field_assets()
    {
        $field = Code::make('Test');

        $this->assertEquals($field->getAssets(), app(AssetManager::class)->getAssets());
    }
}
