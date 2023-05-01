<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use MoonShine\MoonShine;

class ResourceCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:resource {name?} {--m|model=} {--t|title=} {--s|singleton} {--id=}';

    protected $description = 'Create resource';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->createResource();
    }

    /**
     * @throws FileNotFoundException
     */
    public function createResource(): void
    {
        $name = str($this->argument('name') ?? $this->ask('Name'));
        $id = null;

        if ($name->isEmpty()) {
            $name = str($this->ask('Resource name'));
        }

        if ($this->option('singleton')) {
            $id = $this->option('id')
                ?? $this->ask('Item id', 1);
        }

        $name = $name->ucfirst()
            ->replace(['resource', 'Resource'], '')
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $name);
        $title = $this->option('title') ?? $name;

        $resource = $this->getDirectory()."/Resources/{$name}Resource.php";

        $stub = $this->option('singleton')
            ? 'SingletonResource'
            : 'Resource';

        $this->copyStub($stub, $resource, [
            '{namespace}' => MoonShine::namespace('\Resources'),
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            '{id}' => $id,
            'DummyTitle' => $title,
            'Dummy' => $name,
        ]);

        $this->components->info("{$name}Resource file was created: ".str_replace(base_path(), '', $resource));
        $this->components->info('Now register resource in menu');
    }
}
