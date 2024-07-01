<?php

declare(strict_types=1);

namespace MoonShine\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Commands\InstallCommand;
use MoonShine\Menu\MenuItem;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Providers\MoonShineServiceProvider;
use MoonShine\Resources\ModelResource;
use MoonShine\Resources\MoonShineUserResource;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestCoverResource;
use MoonShine\Tests\Fixtures\Resources\TestFileResource;
use MoonShine\Tests\Fixtures\Resources\TestFileResourceWithParent;
use MoonShine\Tests\Fixtures\Resources\TestImageResource;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\WithCustomPages\TestCategoryPageResource;
use MoonShine\Tests\Fixtures\Resources\WithCustomPages\TestCoverPageResource;
use MoonShine\Tests\Fixtures\TestServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithViews;
    use RefreshDatabase;

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

        moonshine()->flushState();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.debug', 'true');
        $app['config']->set('moonshine.cache', 'array');
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
        ModelResource::defaultExportToCsv();

        moonshine()->resources([
            $this->moonShineUserResource(),
            new MoonShineUserRoleResource(),
            new TestCategoryResource(),
            new TestCoverResource(),
            new TestItemResource(),
            new TestCommentResource(),
            new TestImageResource(),
            new TestFileResource(),
            new TestFileResourceWithParent(),

            new TestCategoryPageResource(),
            new TestCoverPageResource(),

            new MoonShineUserRoleResource(),
        ], newCollection: true)
        ->menu([
            MenuItem::make('Admins', $this->moonShineUserResource()),
        ], newCollection: true);

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
            TestServiceProvider::class,
        ];
    }
}
