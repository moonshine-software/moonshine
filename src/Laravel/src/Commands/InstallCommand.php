<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

use function Laravel\Prompts\{confirm, intro, outro, spin, warning};

use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Laravel\Providers\MoonShineServiceProvider;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:install')]
class InstallCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:install {--u|without-user} {--m|without-migrations} {--l|default-layout} {--a|without-auth} {--d|without-notifications} {--t|tests-mode} {--Q|quick-mode}';

    protected $description = 'Install the MoonShine Laravel package';

    private bool $useMigrations = true;

    private bool $authEnabled = true;

    private bool $testsMode = false;

    private bool $quickMode = false;

    public function handle(): int
    {
        intro('MoonShine installation ...');

        if ($this->option('tests-mode')) {
            $this->testsMode = true;
        }

        if ($this->option('quick-mode')) {
            $this->quickMode = true;
        }

        spin(function (): void {
            $this->initVendorPublish(); // assets, config, lang, etc
            $this->initStorage(); // storage:link
            $this->initAuth(); // Authenticate middleware disable/enable
            $this->initMigrations(); // Call artisan migrate after installation or not
            $this->initNotifications(); // Enable/disable notifications, Enable/disable database driver of notifications
            $this->initServiceProvider(); // Create MoonShineServiceProvider and add to bootstrap/providers.php
            $this->initDirectories(); // app/MoonShine
            $this->initDashboard(); // Create app/MoonShine/Pages/Dashboard.php
            $this->initLayout(); // Create app/MoonShine/Layouts/MoonShineLayout.php
        }, 'Installation completed');

        if ($this->useMigrations) {
            $this->call(PublishCommand::class, [
                'type' => 'resources',
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'moonshine-migrations',
                '--force' => true,
            ]);
        }

        if (! $this->testsMode && $this->useMigrations) {
            $this->call('migrate');
        }

        $userCreate = $this->quickMode || $this->confirmAction(
            'Create super user?',
            canRunningInTests: false,
            skipOption: 'without-user',
            condition: fn (): bool => $this->useMigrations && $this->authEnabled,
        );

        if ($userCreate) {
            $this->call(MakeUserCommand::class);
        }

        if (! $this->testsMode && ! $this->quickMode) {
            confirm('Can you quickly star our GitHub repository? 🙏🏻');

            $this->components->bulletList([
                'Star or contribute to MoonShine: https://github.com/moonshine-software/moonshine',
                'MoonShine Documentation: https://getmoonshine.app',
                'CutCode: https://cutcode.dev',
            ]);
        }

        if (! $userCreate && $this->useMigrations && $this->authEnabled) {
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

    protected function initAuth(): void
    {
        $this->authEnabled = $this->quickMode || $this->confirmAction(
            'Enable authentication?',
            skipOption: 'without-auth',
            autoEnable: $this->testsMode,
        );

        if (! $this->authEnabled) {
            $this->replaceInConfig(
                'enabled',
                'false'
            );

            $this->components->task('Authentication disabled');
        }

        if ($this->authEnabled) {
            $this->replaceInConfig(
                'enabled',
                'true'
            );

            $this->components->task('Authentication enabled');
        }
    }

    protected function initMigrations(): void
    {
        $this->useMigrations = $this->quickMode || $this->confirmAction(
            'Install with system migrations?',
            skipOption: 'without-migrations',
            autoEnable: $this->testsMode,
        );

        if (! $this->useMigrations) {
            $this->replaceInConfig(
                'use_migrations',
                'false'
            );

            $this->replaceInConfig(
                'use_database_notifications',
                'false'
            );

            $this->components->task('Installed without default migrations');
        }

        if ($this->useMigrations) {
            $this->replaceInConfig(
                'use_migrations',
                'true'
            );

            $this->replaceInConfig(
                'use_database_notifications',
                'true'
            );

            $this->components->task('Installed with system migrations');
        }
    }

    protected function initNotifications(): void
    {
        $confirm = $this->quickMode || $this->confirmAction(
            'Enable notifications?',
            skipOption: 'without-notifications',
            autoEnable: $this->testsMode,
        );

        $confirmDatabase = $this->quickMode || $this->confirmAction(
            'Use database notifications?',
            canRunningInTests: false,
            condition: fn (): bool => $confirm && $this->useMigrations,
        );

        if (! $confirm) {
            $this->replaceInConfig(
                'use_notifications',
                'false'
            );

            $this->components->task('Notifications disabled');
        }

        if ($confirm) {
            $this->replaceInConfig(
                'use_notifications',
                'true'
            );

            $this->components->task('Notifications enabled');
        }

        if (! $confirmDatabase) {
            $this->replaceInConfig(
                'use_database_notifications',
                'false'
            );
        }

        if ($confirmDatabase) {
            $this->call(NotificationTableCommand::class);

            $this->replaceInConfig(
                'use_database_notifications',
                'true'
            );
        }
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

            $this->registerServiceProvider();

            $this->components->task('Service Provider created');
        }
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            warning(
                "{$this->getDirectory()} directory already exists!",
            );
        }

        $this->makeDir($this->getDirectory('/Resources'));

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
            '--skip-menu' => true,
        ]);

        $this->replaceInConfig(
            'dashboard',
            $this->getNamespace('\Pages\Dashboard') . "::class"
        );

        $this->components->task('Dashboard created');
    }

    protected function initLayout(): void
    {
        $this->call(MakeLayoutCommand::class, array_filter([
            'className' => 'MoonShineLayout',
            '--default' => true,
            '--palette' => $this->quickMode || $this->testsMode ? PurplePalette::class : null,
        ]));

        $this->components->task('Layout published');
    }

    private function confirmAction(
        string $message,
        bool $canRunningInTests = true,
        ?string $skipOption = null,
        ?Closure $condition = null,
        bool $autoEnable = false,
        bool $default = true,
    ): bool {
        if ($autoEnable) {
            return true;
        }

        $additionallyCondition = \is_null($condition) || $condition();

        if (! $canRunningInTests && $this->testsMode) {
            return false;
        }

        $skipByOption = ! \is_null($skipOption) && $this->option($skipOption) === true;

        return $additionallyCondition && ! $skipByOption && confirm($message, default: $default);
    }

    private function registerServiceProvider(): void
    {
        if (
            /** @phpstan-ignore-next-line */
            method_exists(ServiceProvider::class, 'addProviderToBootstrapFile')
            && file_exists(base_path('bootstrap/app.php'))
        ) {
            /** @phpstan-ignore-next-line */
            ServiceProvider::addProviderToBootstrapFile(\App\Providers\MoonShineServiceProvider::class);

            return;
        }

        $this->installServiceProviderAfter(
            'RouteServiceProvider',
            'MoonShineServiceProvider',
        );
    }
}
