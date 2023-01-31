<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Commands;

use Leeto\MoonShine\MoonShine;

class ResourceCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:resource {name?} {--m|model=} {--t|title=}';

    protected $description = 'Create resource';

    public function handle(): void
    {
        $this->createResource();
    }

    public function createResource(): void
    {
        $name = str($this->argument('name'));

        if ($name->isEmpty()) {
            $name = str($this->ask('Resource name'));
        }

        $name = $name->ucfirst()
            ->replace(['resource', 'Resource'], '')
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $name);
        $title = $this->option('title') ?? $name;

        $resource = $this->getDirectory()."/Resources/{$name}Resource.php";
        
        $this->copyStub('Resource', $resource, [
            '{namespace}' => MoonShine::namespace('\Resources'),
            'DummyModel' => $model,
            'DummyTitle' => $title,
            'Dummy' => $name
        ]);
        
        $this->components->info("{$name}Resource file was created: ".str_replace(base_path(), '', $resource));

        $this->components->info('Now register resource in menu');
    }
}
