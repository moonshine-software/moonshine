<?php

declare(strict_types=1);

namespace MoonShine\Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\Commands\InstallCommand;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Providers\MoonShineServiceProvider;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestCoverResource;
use MoonShine\Tests\Fixtures\Resources\TestFileResource;
use MoonShine\Tests\Fixtures\Resources\TestFileResourceWithParent;
use MoonShine\Tests\Fixtures\Resources\TestHasManyCommentResource;
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

    protected CoreContract $moonshineCore;

    protected MoonShineRequest $moonshineRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moonshineCore = $this->app->make(CoreContract::class);
        $this->moonshineRequest = $this->moonshineCore->getContainer(MoonShineRequest::class);
        $this->moonshineCore->flushState();

        $this->performApplication()
            ->resolveFactories()
            ->resolveSuperUser()
            ->resolveMoonShineUserResource()
            ->registerTestResource();
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
            '--default-layout' => true,
        ]);

        $this->artisan('optimize:clear');

        return $this;
    }

    protected function resolveFactories(): static
    {
        Factory::guessFactoryNamesUsing(function ($factory): string {
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
        $this->moonShineUserResource = $this->moonshineCore->getContainer(MoonShineUserResource::class);

        return $this;
    }

    protected function registerTestResource(): static
    {
        $this->moonshineCore->resources([
            $this->moonShineUserResource(),
            MoonShineUserRoleResource::class,
            TestCategoryResource::class,
            TestCoverResource::class,
            TestItemResource::class,
            TestCommentResource::class,
            TestImageResource::class,
            TestFileResource::class,
            TestFileResourceWithParent::class,

            TestCategoryPageResource::class,
            TestCoverPageResource::class,

            MoonShineUserRoleResource::class,

            TestHasManyCommentResource::class,
        ], newCollection: true)
        ->pages([
            ...$this->moonshineCore->getConfig()->getPages(),
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
            TestServiceProvider::class,
        ];
    }
}
