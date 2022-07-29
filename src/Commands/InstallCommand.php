<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Support\Facades\Artisan;
use Leeto\MoonShine\Providers\MoonShineServiceProvider;

class InstallCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:install';

    protected $description = 'Install the moonshine package';

    public function handle(): void
    {
        $this->components->info('MoonShine installation ...');

        $this->initDirectories();
        $this->initDashboard();

        $this->components->info('Installation completed');

        $this->components->info("Now run 'php artisan moonshine:user'");
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            $this->components->warn("{$this->getDirectory()} directory already exists!");
        }

        $this->makeDir('Resources');

        $this->components->task('Resources directory created');

        Artisan::call('vendor:publish', [
            '--provider' => MoonShineServiceProvider::class,
            '--force' => true,
        ]);

        $this->components->task('Vendor published');

        Artisan::call('migrate');

        $this->components->task('Tables migrated');

        Artisan::call('storage:link');

        $this->components->task('Storage link created');
    }

    protected function initDashboard(): void
    {
        $dashboard = $this->getDirectory() . "/Dashboard.php";
        $contents = $this->getStub('Dashboard');

        $this->laravel['files']->put($dashboard, $contents);

        $this->components->task('Dashboard created');
    }
}
