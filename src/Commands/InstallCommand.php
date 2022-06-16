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
        $this->comment('MoonShine installation ...');

        $this->initDirectories();
        $this->initDashboard();

        $this->info('Installation completed');

        $this->comment("Now run 'php artisan moonshine:user'");
    }

    protected function initDirectories(): void
    {
        if (is_dir($this->getDirectory())) {
            $this->error("{$this->getDirectory()} directory already exists!");
        }

        $this->makeDir('Resources');

        Artisan::call('vendor:publish', [
            '--provider' => MoonShineServiceProvider::class,
            '--force' => true,
        ]);

        Artisan::call('migrate');
        Artisan::call('storage:link');
    }

    protected function initDashboard(): void
    {
        $dashboard = $this->getDirectory() . "/Dashboard.php";
        $contents = $this->getStub('Dashboard');

        $this->laravel['files']->put($dashboard, $contents);
    }
}
