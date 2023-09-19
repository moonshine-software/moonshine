<?php

declare(strict_types=1);

namespace MoonShine\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use MoonShine\Commands\InstallCommand;
use MoonShine\Menu\MenuItem;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\MoonShine;
use MoonShine\Providers\MoonShineServiceProvider;
use MoonShine\Resources\ModelResource;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestCoverResource;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected Authenticatable|MoonshineUser $adminUser;

    protected ModelResource $moonShineUserResource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->performApplication()
            ->resolveFactories()
            ->resolveSuperUser()
            ->resolveMoonShineUserResource()
            ->registerTestResource();
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.debug', 'true');

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.database', 'moonshine_tests');
        $app['config']->set('database.connections.mysql.username', 'root');
    }

    protected function performApplication(): static
    {
        $this->artisan(InstallCommand::class, [
            '--without-user' => true,
            '--without-migrations' => true,
        ]);

        $this->artisan('config:clear');
        $this->artisan('view:clear');
        $this->artisan('cache:clear');

        $this->refreshApplication();
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(realpath('./database/migrations'));
        $this->loadMigrationsFrom(realpath('./tests/Fixtures/Migrations'));

        return $this;
    }

    protected function resolveFactories(): static
    {
        Factory::guessFactoryNamesUsing(static function ($factory): string {
            $factoryBasename = class_basename($factory);

            return "MoonShine\Database\Factories\\$factoryBasename" . 'Factory';
        });

        return $this;
    }

    public function superAdminAttributes(): array
    {
        return [
            'id' => 1,
            'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => bcrypt('test'),
        ];
    }

    protected function resolveSuperUser(): static
    {
        $this->adminUser = MoonshineUser::factory()
            ->create($this->superAdminAttributes())
            ->load('moonshineUserRole');

        return $this;
    }

    protected function resolveMoonShineUserResource(): static
    {
        $this->moonShineUserResource = new MoonShineUserResource();

        return $this;
    }

    protected function registerTestResource(): static
    {
        MoonShine::resources([
            $this->moonShineUserResource(),
            new MoonShineUserRoleResource(),
            new TestCategoryResource(),
            new TestCoverResource(),
            new TestItemResource(),
            new TestCommentResource(),
            new TestImageResource(),

            new MoonShineUserRoleResource(),
        ]);

        MoonShine::menu([
            MenuItem::make('Admins', $this->moonShineUserResource()),
        ]);

        return $this;
    }

    public function moonShineUserResource(): ModelResource
    {
        return $this->moonShineUserResource;
    }

    public function adminUser(): Model|Builder|Authenticatable
    {
        return $this->adminUser;
    }

    protected function getPackageProviders($app): array
    {
        return [
            MoonShineServiceProvider::class,
        ];
    }
}
