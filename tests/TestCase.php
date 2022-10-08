<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Leeto\MoonShine\Menu\MenuItem;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\Models\MoonshineUserRole;
use Leeto\MoonShine\MoonShine;
use Leeto\MoonShine\Providers\MoonShineServiceProvider;
use Leeto\MoonShine\Resources\FileLogViewerResource;
use Leeto\MoonShine\Resources\ModelResource;
use Leeto\MoonShine\Resources\MoonShineUserResource;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use InteractsWithViews;
    use WithAuthTesting;

    protected Authenticatable|MoonshineUser $adminUser;

    protected ModelResource $testResource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->performApplication()
            ->resolveFactories()
            ->resolveSuperUser()
            ->resolveTestResource()
            ->registerTestResource();
    }

    protected function performApplication(): static
    {
        $this->artisan('moonshine:install');
        $this->artisan('config:clear');
        $this->artisan('cache:clear');

        $this->refreshApplication();
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(realpath('./database/migrations'));

        return $this;
    }

    protected function resolveFactories(): static
    {
        Factory::guessFactoryNamesUsing(function ($factory) {
            $factoryBasename = class_basename($factory);

            return "Leeto\MoonShine\Database\Factories\\$factoryBasename".'Factory';
        });

        return $this;
    }

    protected function superAdminAttributes(): array
    {
        return [
            'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('test')
        ];
    }

    protected function resolveSuperUser(): static
    {
        $this->adminUser = MoonshineUser::factory()
            ->create($this->superAdminAttributes())
            ->load('moonshineUserRole');

        return $this;
    }

    protected function adminUser(): Model|Builder|Authenticatable
    {
        return $this->adminUser;
    }

    protected function resolveTestResource(): static
    {
        $this->testResource = new MoonShineUserResource();

        return $this;
    }

    protected function testResource(): ModelResource
    {
        return $this->testResource;
    }

    protected function registerTestResource(): static
    {
        MoonShine::resources([
            $this->testResource(),
        ]);

        MoonShine::menu([
            MenuItem::make($this->testResource())
        ]);

        return $this;
    }

    protected function getPackageProviders($app): array
    {
        return [
            MoonShineServiceProvider::class
        ];
    }
}
