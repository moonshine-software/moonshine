<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use JsonException;

use function Laravel\Prompts\{select, text};

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Support\StubsPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:page')]
class MakePageCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:page {className?} {--force} {--without-register} {--skip-menu} {--crud} {--json} {--without-output} {--dir=} {--extends=} {--base-dir=} {--base-namespace=} {--resource=}';

    protected $description = 'Create page';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $extends = $this->option('extends') ?? 'Page';

        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $stubsPath = new StubsPath($className, 'php');

        $dir = $this->option('dir') ?: 'Pages';
        $resource = $this->option('resource') ?: ModelResource::class;

        $stubsPath = $this->qualifyStubsDir($stubsPath, $dir);

        if (! $this->option('force') && ! $this->option('extends') && ! $this->option('crud')) {
            $types = [
                '' => 'Custom',
                'IndexPage' => 'IndexPage',
                'FormPage' => 'FormPage',
                'DetailPage' => 'DetailPage',
            ];

            $type = array_search(
                select('Type', $types),
                $types,
                true
            );

            $extends = $type ?: null;
            $stub = match ($extends) {
                'IndexPage' => 'CrudIndexPage',
                'FormPage' => 'CrudFormPage',
                'DetailPage' => 'CrudDetailPage',
                default => 'Page'
            };

            $this->makePage($stubsPath, $stub, $extends, $resource);

            return self::SUCCESS;
        }

        if ($this->option('crud')) {
            $name = $stubsPath->name;

            if ($this->option('dir') === null) {
                $dir = "Resources/$className/Pages";
            }

            foreach (['IndexPage', 'FormPage', 'DetailPage'] as $type) {
                $stubsPath = new StubsPath("$name$type", 'php');

                $stubsPath->prependDir(
                    $this->getDirectory($dir),
                )->prependNamespace(
                    $this->getNamespace($dir),
                );

                $this->makePage($stubsPath, "Crud$type", $type, $resource);
            }

            return self::SUCCESS;
        }

        $this->makePage($stubsPath, 'Page', $extends);

        return self::SUCCESS;
    }

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    private function makePage(
        StubsPath $stubsPath,
        string $stub = 'Page',
        ?string $extends = null,
        string $resource = ModelResource::class,
    ): void {
        $extends = $extends ?: 'Page';

        $this->makeDir($stubsPath->dir);

        $uses = '';

        if ($this->option('skip-menu')) {
            $uses = '#[\MoonShine\MenuManager\Attributes\SkipMenu]';
        }

        $this->copyStub($stub, $stubsPath->getPath(), [
            '{namespace}' => $stubsPath->namespace,
            '{resource-namespace}' => $resource,
            '{resourceShort}' => class_basename($resource),
            'DummyClass' => $stubsPath->name,
            'DummyTitle' => $stubsPath->name,
            '{extendShort}' => $extends,
            '{uses}' => $uses,
        ]);

        $this->wasCreatedInfo($stubsPath);

        if ($extends === 'Page' && ! $this->option('without-register')) {
            self::addResourceOrPageToProviderFile(
                $stubsPath->name,
                page: true,
                namespace: $stubsPath->namespace
            );
        }

        $this->formatOutput();
    }
}
