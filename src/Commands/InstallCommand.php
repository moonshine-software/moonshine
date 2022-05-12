<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;

class InstallCommand extends BaseMoonShineCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'moonshine:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the moonshine package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initDirectory();
    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initDirectory()
    {
        $this->directory = config('moonshine.dir', $this->directory);

        if (is_dir($this->directory)) {
            $this->error("$this->directory directory already exists!");

            return;
        }

        $this->makeDir('/');
        $this->info('Directory was created:' . str_replace(base_path(), '', $this->directory));

        $this->makeDir('Controllers');
        $this->makeDir('Resources');
    }
}
