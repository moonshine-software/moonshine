<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\{confirm, text};

use MoonShine\Laravel\Support\StubsPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:layout')]
class MakeLayoutCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:layout {className?} {--default} {--dir=} {--base-dir=} {--base-namespace=}';

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

        $this->copyStub('Layout', $stubsPath->getPath(), [
            '{namespace}' => $stubsPath->namespace,
            '{extend}' => $extends,
            '{extendShort}' => class_basename($extends),
            'DummyClass' => $stubsPath->name,
        ]);

        $this->wasCreatedInfo($stubsPath);

        if ($this->option('default') || confirm('Use the default template in the system?')) {
            $this->replaceInConfig(
                'layout',
                $stubsPath->getClassString()
            );
        }

        return self::SUCCESS;
    }
}
