<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use MoonShine\Laravel\Providers\MoonShineServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\{confirm, intro, outro, spin, warning};

#[AsCommand(name: 'moonshine:install')]
class InstallCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:install {--u|without-user} {--m|without-migrations}';

    protected $description = 'Install the moonshine package';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        intro('MoonShine installation ...');

        spin(function (): void {
            $this->initVendorPublish();
            $this->initStorage();
            $this->initServiceProvider();
            $this->initDirectories();
            $this->initDashboard();
            $this->initMigrations();
        }, 'Installation completed');

        $userCreated = false;

        if (! $this->option('without-user') && confirm('Create super user ?')) {
            $this->call(MakeUserCommand::class);
            $userCreated = true;
        }

        if (! moonshine()->runningUnitTests()) {
            confirm('Can you quickly star our GitHub repository? 🙏🏻', true);

            $this->components->bulletList([
                'Star or contribute to MoonShine: https://github.com/moonshine-software/moonshine',
                'MoonShine Documentation: https://moonshine-laravel.com',
                'CutCode: https://cutcode.dev',
            ]);
        }

        if (! $userCreated) {
            $this->components->task('');
            outro("Now run 'php artisan moonshine:user'");
        }

        return self::SUCCESS;
    }

    protected function initVendorPublish(): void
    {
        $this->call('vendor:publish', [
            '--provider' => MoonShineServiceProvider::class,
            '--force' => true,
        ]);

        $this->components->task('Vendor published');
    }

    protected function initStorage(): void
    {
        $this->call('storage:link');

        $this->components->task('Storage link created');
    }

    /**
     * @throws FileNotFoundException
     */
    protected function initServiceProvider(): void
    {
        $this->comment('Publishing MoonShine Service Provider...');
        $this->call('vendor:publish', ['--tag' => 'moonshine-provider']);

        if (! File::exists(app_path('Providers/MoonShineServiceProvider.php'))) {
            $this->copyStub(
                'MoonShineServiceProvider',
                app_path('Providers/MoonShineServiceProvider.php')
            );

            if (! moonshine()->runningUnitTests()) {
                $this->registerServiceProvider();
            }

            $this->components->task('Service Provider created');
        }
    }

    protected function registerServiceProvider(): void
    {
        if (method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')) {
            ServiceProvider::addProviderToBootstrapFile(\App\Providers\MoonShineServiceProvider::class); // @phpstan-ignore-line

            return;
        }

        $this->installServiceProviderAfter(
            'RouteServiceProvider',
            'MoonShineServiceProvider'
        );
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            warning(
                "{$this->getDirectory()} directory already exists!"
            );
        }

        $this->makeDir($this->getDirectory() . '/Resources');

        $this->components->task('Resources directory created');
    }

    /**
     * @throws FileNotFoundException
     */
    protected function initDashboard(): void
    {
        $this->call(MakePageCommand::class, [
            'className' => 'Dashboard',
            '--force' => true,
        ]);

        $this->replaceInFile(
            "'dashboard' => 'Dashboard::class'",
            "'dashboard' => " . moonshineConfig()->getNamespace('\Pages\Dashboard') . "::class",
            config_path('moonshine.php')
        );

        $this->components->task('Dashboard created');
    }

    protected function initMigrations(): void
    {
        $confirm = ! $this->option('without-migrations') && confirm('Install migrations?');

        if (moonshineConfig()->isUseMigrations() && $confirm) {
            $this->call('migrate');

            $this->components->task('Tables migrated');
        } else {
            $this->components->task('Installed without default migrations');
        }
    }
}
