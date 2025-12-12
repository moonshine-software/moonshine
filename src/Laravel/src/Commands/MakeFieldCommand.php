<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\{select, text};

use MoonShine\Laravel\Support\StubsPath;
use MoonShine\UI\Fields\Field;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand(name: 'moonshine:field')]
class MakeFieldCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:field {className?} {--view=} {--extends=} {--base-dir=} {--base-namespace=}';

    protected $description = 'Create field';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            'CustomField',
            required: true,
        );

        $stubsPath = new StubsPath($className, 'php');

        $view = $this->makeViewFromStub('admin.fields', $stubsPath->name, $stubsPath->dir);

        $stubsPath = $this->qualifyStubsDir($stubsPath, 'Fields');

        $extends = $this->option('extends') ?? select('Extends', $this->findExtends(), Field::class);

        $this->makeDir($stubsPath->dir);

        $this->copyStub('Field', $stubsPath->getPath(), [
            '{namespace}' => $stubsPath->namespace,
            '{view}' => $view,
            '{extend}' => $extends,
            '{extendShort}' => class_basename($extends),
            'DummyClass' => $stubsPath->name,
        ]);

        $this->wasCreatedInfo($stubsPath);

        return self::SUCCESS;
    }

    private function findExtends(): array
    {
        return Collection::make(File::files(__DIR__ . '/../../../UI/src/Fields/'))
            ->mapWithKeys(
                static fn (SplFileInfo $file): array => [
                    $file->getFilenameWithoutExtension() => $file->getFilenameWithoutExtension(),
                ],
            )
            ->except(['Field', 'Fields', 'FormElement'])
            ->mapWithKeys(static fn ($file): array => [('MoonShine\UI\Fields\\' . $file) => $file])
            ->prepend('Base', Field::class)
            ->toArray();
    }
}
