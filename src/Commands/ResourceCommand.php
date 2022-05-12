<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ResourceCommand extends BaseMoonShineCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'moonshine:resource {name?} {--m|model=} {--t|title=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create resource';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createResource();
    }

    public function createResource()
    {
        $this->directory = config('moonshine.dir', $this->directory);

        $name = str($this->argument('name'));

        if(!$name) {
            $name = str($this->ask('Resource name'));
        }

        $name = $name->ucfirst()
            ->replace(['resource', 'Resource'], '');

        $model = $this->option('model') ?? $name;
        $title = $this->option('title') ?? $name;

        $resource = $this->directory."/Resources/{$name}Resource.php";
        $contents = $this->getStub('Resource');
        $contents = str_replace('DummyModel', $model, $contents);
        $contents = str_replace('DummyTitle', $title, $contents);

        $this->laravel['files']->put(
            $resource,
            str_replace('Dummy', $name, $contents)
        );

        $this->info("{$name}Resource file was created: " . str_replace(base_path(), '', $resource));

        $controller = $this->directory."/Controllers/{$name}Controller.php";
        $contents = $this->getStub('ResourceController');

        $this->laravel['files']->put(
            $controller,
            str_replace('Dummy', $name, $contents)
        );

        $this->info("{$name}Controller file was created: " . str_replace(base_path(), '', $controller));
    }
}
