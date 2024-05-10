<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:layout')]
class MakeLayoutCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:layout {className?} {--force} {--dir=}';

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

        $dir = $this->option('dir') ?: dirname($className);
        $className = class_basename($className);

        if ($dir === '.') {
            $dir = 'Layouts';
        }

        $layout = $this->getDirectory() . "/$dir/$className.php";

        if (! is_dir($this->getDirectory() . "/$dir")) {
            $this->makeDir($this->getDirectory() . "/$dir");
        }

        $this->copyStub('Layout', $layout, [
            '{namespace}' => moonshineConfig()->getNamespace('\\' . str_replace('/', '\\', $dir)),
            'DummyLayout' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $layout
            )
        );

        return self::SUCCESS;
    }
}
