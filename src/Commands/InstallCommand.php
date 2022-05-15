<?php

namespace Leeto\MoonShine\Commands;

class InstallCommand extends BaseMoonShineCommand
{
    protected $signature = 'moonshine:install';

    protected $description = 'Install the moonshine package';

    public function handle(): void
    {
        $this->initDirectories();
    }

    protected function initDirectories(): void
    {
        $this->directory = config('moonshine.dir', $this->directory);

        if (is_dir($this->getDirectory())) {
            $this->error("{$this->getDirectory()} directory already exists!");
        }

        $this->makeDir('/');
        $this->info('Directory was created:' . str_replace(base_path(), '', $this->getDirectory()));

        $this->makeDir('Controllers');
        $this->makeDir('Resources');
    }
}
