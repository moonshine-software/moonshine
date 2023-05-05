<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Artisan;
use MoonShine\MoonShine;
use MoonShine\Providers\MoonShineServiceProvider;

class InstallCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:install';

    protected $description = 'Install the moonshine package';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->components->info('MoonShine installation ...');

        $this->initDirectories();
        $this->initDashboard();
        $this->initServiceProvider();

        $this->components->info('Installation completed');

        $this->components->info("Now run 'php artisan moonshine:user'");
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            $this->components->warn("{$this->getDirectory()} directory already exists!");
        }

        $this->makeDir($this->getDirectory() . '/Resources');

        $this->components->task('Resources directory created');

        Artisan::call('vendor:publish', [
            '--provider' => MoonShineServiceProvider::class,
            '--force' => true,
        ]);

        $this->components->task('Vendor published');

        Artisan::call('storage:link');

        $this->components->task('Storage link created');

        if (config('moonshine.use_migrations', true) && config('moonshine.auth.enable', true)) {
            Artisan::call('migrate');

            $this->components->task('Tables migrated');
        } else {
            $this->components->task('Auth disabled, installed without database');
        }

        if (!app()->runningUnitTests()) {
            $this->choice('Can you quickly star our GitHub repository? 🙏🏻', [
                'yes', 'no'
            ], 'yes');

            $this->components->bulletList([
                'Star or contribute to MoonShine: https://github.com/moonshine-software/moonshine',
                'MoonShine Documentation: https://moonshine.cutcode.dev',
                'CutCode: https://cutcode.dev'
            ]);
        }
    }

    /**
     * @throws FileNotFoundException
     */
    protected function initDashboard(): void
    {
        $this->copyStub('Dashboard', $this->getDirectory().'/Dashboard.php', [
            '{namespace}' => MoonShine::namespace(),
        ]);

        $this->components->task('Dashboard created');
    }

    /**
     * @throws FileNotFoundException
     */
    protected function initServiceProvider(): void
    {
        $this->comment('Publishing MoonShine Service Provider...');
        Artisan::call('vendor:publish', ['--tag' => 'moonshine-provider']);

        $this->copyStub('MoonShineServiceProvider', app_path('Providers/MoonShineServiceProvider.php'));

        if (! app()->runningUnitTests()) {
            $this->registerServiceProvider();
        }

        $this->components->task('Service Provider created');
    }

    protected function registerServiceProvider(): void
    {
        $this->installServiceProviderAfter(
            'RouteServiceProvider',
            'MoonShineServiceProvider'
        );
    }
}
