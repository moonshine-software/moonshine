<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\{confirm, select, text};

use MoonShine\ColorManager\Palettes\PurplePalette;
use MoonShine\Contracts\ColorManager\PaletteContract;
use MoonShine\Laravel\Support\StubsPath;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:layout')]
class MakeLayoutCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:layout {className?} {--default} {--palette=} {--dir=} {--base-dir=} {--base-namespace=}';

    protected $description = 'Create layout';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $stubsPath = new StubsPath($className, 'php');

        $dir = $this->option('dir') ?: 'Layouts';

        $stubsPath = $this->qualifyStubsDir($stubsPath, $dir);

        $this->makeDir($stubsPath->dir);

        $extendClassName = 'AppLayout';
        $extends = "MoonShine\Laravel\Layouts\\$extendClassName";

        $palette = $this->option('palette') ?: select('Select a palette', $this->findPalettes(), PurplePalette::class);

        $this->copyStub('Layout', $stubsPath->getPath(), [
            '{namespace}' => $stubsPath->namespace,
            '{extend}' => $extends,
            '{extendShort}' => class_basename($extends),
            '{palette}' => $palette,
            '{paletteShort}' => class_basename($palette),
            'DummyClass' => $stubsPath->name,
        ]);

        $this->wasCreatedInfo($stubsPath);

        if ($this->option('default') || confirm('Use the default template in the system?')) {
            $this->replaceInConfig(
                'layout',
                $stubsPath->getClassString()
            );

            $this->replaceInConfig(
                'palette',
                "$palette::class"
            );
        }

        return self::SUCCESS;
    }

    private function findPalettes(): array
    {
        $paletteInstance = static fn (string $name): PaletteContract => new ('MoonShine\ColorManager\Palettes\\' . $name);

        return Collection::make(File::files(__DIR__ . '/../../../ColorManager/src/Palettes/'))
            ->mapWithKeys(
                static fn (SplFileInfo $file): array => [
                    $file->getFilenameWithoutExtension() => str_replace('Palette', '', $file->getFilenameWithoutExtension())
                        . " ({$paletteInstance($file->getFilenameWithoutExtension())->getDescription()})",
                ],
            )
            ->mapWithKeys(static fn (string $description, string $title): array => [('MoonShine\ColorManager\Palettes\\' . $title) => $description])
            ->toArray();
    }
}
