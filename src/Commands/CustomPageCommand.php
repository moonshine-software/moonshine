<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use MoonShine\MoonShine;

class CustomPageCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:custom {name?} {--a|alias=} {--t|title=} {--view=}';

    protected $description = 'Create custom page';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->createCustomPage();
    }

    /**
     * @throws FileNotFoundException
     */
    public function createCustomPage(): void
    {
        $name = str($this->argument('name') ?? $this->ask('Name'));

        $plular = $name->singular()->plural();

        $title = $this->option('title') ?? $plular->value();

        $alias = $this->option('alias') ?? $plular->kebab()->lower()->value();

        $view = $this->option('view') ?? $plular->snake()->lower()->value();

        $resource = $this->getDirectory() . "/Resources/{$name}CustomPage.php";

        $this->copyStub('CustomPage', $resource, [
            '{namespace}' => MoonShine::namespace('\Resources'),
            'DummyTitle' => $title,
            'DummyAlias' => $alias,
            'DummyView' => $view,
            'Dummy' => $name,
        ]);

        $this->components->info(
            "{$name}CustomPage file was created: " . str_replace(
                base_path(),
                '',
                $resource
            )
        );
        $this->components->info('Now register custom page in menu');
    }
}
