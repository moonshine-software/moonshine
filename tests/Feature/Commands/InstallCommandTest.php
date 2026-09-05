<?php

declare(strict_types=1);

namespace MoonShine\Tests\Feature\Commands;

use Illuminate\Filesystem\Filesystem;
use MoonShine\Laravel\Commands\InstallCommand;
use MoonShine\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use ReflectionMethod;

#[CoversClass(InstallCommand::class)]
#[Group('commands')]
final class InstallCommandTest extends TestCase
{
    #[Test]
    public function registersProviderInModernAndLegacyApplicationStructures(): void
    {
        $bootstrapPath = $this->app->bootstrapPath();
        $configPath = $this->app->configPath();
        $directory = sys_get_temp_dir() . '/moonshine-provider-' . uniqid();
        $files = new Filesystem();
        $files->makeDirectory($directory);

        try {
            $this->app->useBootstrapPath($directory);
            $this->app->useConfigPath($directory);

            foreach ([true, false] as $modern) {
                $files->put($directory . '/app.php', '<?php return [App\\Providers\\RouteServiceProvider::class,];');

                if ($modern) {
                    $files->put($directory . '/providers.php', '<?php return [];');
                } else {
                    $files->delete($directory . '/providers.php');
                }

                $command = $this->app->make(InstallCommand::class);
                $register = new ReflectionMethod(InstallCommand::class, 'registerServiceProvider');
                $register->invoke($command);
                $register->invoke($command);

                $contents = $files->get($directory . ($modern ? '/providers.php' : '/app.php'));
                $this->assertSame(1, substr_count($contents, 'App\\Providers\\MoonShineServiceProvider::class'));
            }
        } finally {
            $this->app->useBootstrapPath($bootstrapPath);
            $this->app->useConfigPath($configPath);
            $files->deleteDirectory($directory);
        }
    }

    #[Test]
    #[TestDox('it successful installed')]
    public function successfulCreated(): void
    {
        $this->artisan(InstallCommand::class, [
            '--tests-mode' => true,
        ])
            ->expectsOutputToContain('Vendor published')
            ->expectsOutputToContain('Storage link created')
            ->expectsOutputToContain('Resources directory created')
            ->expectsOutputToContain('Dashboard created')
            ->expectsOutputToContain('Layout published')
            ->expectsOutputToContain('Installation completed')
            ->assertSuccessful()
        ;
    }
}
