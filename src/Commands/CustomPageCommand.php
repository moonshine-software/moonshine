<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use MoonShine\MoonShine;

class CustomPageCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:page {name?} {--a|alias=} {--t|title=} {--view=}';

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

        $title = $this->option('title') ?? $name->singular()->plural()->value();

        $alias = $this->option('alias') ?? $name->kebab()->lower()->value();

        $view = $this->option('view') ?? '';

        $resource = $this->getDirectory() . "/Pages/{$name}.php";

        File::ensureDirectoryExists(dirname($resource));

        $this->copyStub('CustomPage', $resource, [
            '{namespace}' => MoonShine::namespace('\Pages'),
            'DummyTitle' => $title,
            'DummyAlias' => $alias,
            'DummyView' => $view,
            'Dummy' => $name,
        ]);

        $this->components->info(
            "{$name} file was created: " . str_replace(
                base_path(),
                '',
                $resource
            )
        );
        $this->components->info('Now register page in menu');
    }
}
