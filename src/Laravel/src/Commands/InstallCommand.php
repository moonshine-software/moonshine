<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

use function Laravel\Prompts\{confirm, intro, outro, spin, warning};

use MoonShine\Laravel\Providers\MoonShineServiceProvider;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:install')]
class InstallCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:install {--u|without-user} {--m|without-migrations} {--l|default-layout} {--without-auth} {--without-notifications} {--tests-mode}';

    protected $description = 'Install the moonshine package';

    private bool $useMigrations = true;

    private bool $authEnabled = true;

    private bool $testsMode = false;

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        intro('MoonShine installation ...');

        if ($this->option('tests-mode')) {
            $this->testsMode = true;
        }

        spin(function (): void {
            $this->initVendorPublish();
            $this->initStorage();
            $this->initMigrations();
            $this->initAuth();
            $this->initNotifications();
            $this->initServiceProvider();
            $this->initDirectories();
            $this->initDashboard();
            $this->initLayout();
        }, 'Installation completed');

        $userCreated = false;

        if (! $this->testsMode && $this->useMigrations && $this->authEnabled && ! $this->option(
            'without-user',
        ) && confirm('Create super user ?')) {
            $this->call(MakeUserCommand::class);
            $userCreated = true;
        }

        if ($this->useMigrations) {
            $this->call(PublishCommand::class, [
                'type' => 'resources',
            ]);
        }

        if (! moonshine()->runningUnitTests()) {
            confirm('Can you quickly star our GitHub repository? 🙏🏻', true);

            $this->components->bulletList([
                'Star or contribute to MoonShine: https://github.com/moonshine-software/moonshine',
                'MoonShine Documentation: https://moonshine-laravel.com',
                'CutCode: https://cutcode.dev',
            ]);
        }

        if (! $userCreated && $this->useMigrations && $this->authEnabled) {
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

        $replace = [];

        if (! $this->useMigrations) {
            $replace = [
                "use " . MoonShineUserResource::class . ";\n" => '',
                "use " . MoonShineUserRoleResource::class . ";\n" => '',
                "MoonShineUserResource::class," => '',
                "MoonShineUserRoleResource::class," => '',
            ];
        }

        if (! File::exists(app_path('Providers/MoonShineServiceProvider.php'))) {
            $this->copyStub(
                'MoonShineServiceProvider',
                app_path('Providers/MoonShineServiceProvider.php'),
                $replace,
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
            // @phpstan-ignore-next-line
            ServiceProvider::addProviderToBootstrapFile(\App\Providers\MoonShineServiceProvider::class);

            return;
        }

        $this->installServiceProviderAfter(
            'RouteServiceProvider',
            'MoonShineServiceProvider',
        );
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            warning(
                "{$this->getDirectory()} directory already exists!",
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
            '--without-register' => true,
        ]);

        $this->replaceInFile(
            "'dashboard' => Dashboard::class",
            "'dashboard' => " . moonshineConfig()->getNamespace('\Pages\Dashboard') . "::class",
            config_path('moonshine.php'),
        );

        $this->components->task('Dashboard created');
    }

    protected function initAuth(): void
    {
        if ($this->testsMode) {
            return;
        }

        $confirm = $this->useMigrations && ! $this->option('without-auth') && confirm('Enable authentication?');

        if ($confirm) {
            $this->components->task('Authentication enabled');

            $this->authEnabled = true;

            $this->replaceInFile(
                "'enabled' => false,",
                "'enabled' => true,",
                config_path('moonshine.php'),
            );
        } else {
            $this->components->task('Authentication disabled');

            $this->authEnabled = false;

            $this->replaceInFile(
                "'enabled' => true,",
                "'enabled' => false,",
                config_path('moonshine.php'),
            );
        }
    }

    protected function initNotifications(): void
    {
        if ($this->testsMode) {
            return;
        }

        $confirm = $this->useMigrations && ! $this->option('without-notifications') && confirm('Enable notifications?');

        if ($confirm) {
            $this->components->task('Notifications enabled');

            $this->replaceInFile(
                "'use_notifications' => false,",
                "'use_notifications' => true,",
                config_path('moonshine.php'),
            );

            $this->replaceInFile(
                "'use_database_notifications' => false,",
                "'use_database_notifications' => true,",
                config_path('moonshine.php'),
            );
        } else {
            $this->components->task('Notifications disabled');

            $this->replaceInFile(
                "'use_notifications' => true,",
                "'use_notifications' => false,",
                config_path('moonshine.php'),
            );

            $this->replaceInFile(
                "'use_database_notifications' => true,",
                "'use_database_notifications' => false,",
                config_path('moonshine.php'),
            );
        }
    }

    protected function initMigrations(): void
    {
        if ($this->testsMode) {
            $this->call('migrate');

            return;
        }

        $confirm = ! $this->option('without-migrations') && confirm('Install migrations?');

        if ($confirm) {
            $this->call('migrate');

            $this->components->task('Tables migrated');

            $this->useMigrations = true;

            $this->replaceInFile(
                "'use_migrations' => false,",
                "'use_migrations' => true,",
                config_path('moonshine.php'),
            );

            $this->replaceInFile(
                "'use_database_notifications' => false,",
                "'use_database_notifications' => true,",
                config_path('moonshine.php'),
            );
        } else {
            $this->components->task('Installed without default migrations');

            $this->useMigrations = false;

            $this->replaceInFile(
                "'use_migrations' => true,",
                "'use_migrations' => false,",
                config_path('moonshine.php'),
            );

            $this->replaceInFile(
                "'use_database_notifications' => true,",
                "'use_database_notifications' => false,",
                config_path('moonshine.php'),
            );
        }
    }

    protected function initLayout(): void
    {
        $compact = ! $this->testsMode && ! $this->option('default-layout') && confirm(
            'Want to use a minimalist theme?',
        );

        $this->call(MakeLayoutCommand::class, [
            'className' => 'MoonShineLayout',
            '--compact' => $compact,
            '--full' => ! $compact,
            '--default' => true,
        ]);

        $this->components->task('Layout published');
    }
}
