<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\confirm;

use function Laravel\Prompts\text;

use MoonShine\MoonShine;

class MakeResourceCommand extends MoonShineCommand
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
        $name = str(
            text(
                'Name',
                'ArticleResource',
                $this->argument('name') ?? '',
                required: true,
            )
        );

        $id = null;

        if ($isSingleton = confirm('Singleton?', required: true)) {
            $id = text('Item id', default: $this->option('id') ?? '', required: true);
        }

        $name = $name->ucfirst()
            ->replace(['resource', 'Resource'], '')
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $name);
        $title = $this->option('title') ??
            ($isSingleton ? $name
                : str($name)->singular()->plural()->value());

        $resource = $this->getDirectory() . "/Resources/{$name}Resource.php";

        $stub = $isSingleton
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

        $this->components->info(
            "{$name}Resource file was created: " . str_replace(
                base_path(),
                '',
                $resource
            )
        );
        $this->components->info('Now register resource in menu');
    }
}
