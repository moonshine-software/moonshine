<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

#[AsCommand(name: 'moonshine:component')]
class MakeComponentCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:component {className?}';

    protected $description = 'Create component';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $suggestView = str($className)
            ->classBasename()
            ->kebab()
            ->prepend("admin.components.")
            ->value();

        $view = text(
            'Path to view',
            $suggestView,
            default: $suggestView,
            required: true
        );

        $component = $this->getDirectory() . "/Components/$className.php";

        if(! is_dir($this->getDirectory() . '/Components')) {
            $this->makeDir($this->getDirectory() . '/Components');
        }

        $view = str_replace('.blade.php', '', $view);
        $viewPath = resource_path('views/' . str_replace('.', DIRECTORY_SEPARATOR, $view));
        $viewPath .= '.blade.php';

        if(! is_dir(dirname($viewPath))) {
            $this->makeDir(dirname($viewPath));
        }

        $this->copyStub('view', $viewPath);

        $this->copyStub('Component', $component, [
            '{namespace}' => moonshineConfig()->getNamespace('\Components'),
            '{view}' => $view,
            'DummyClass' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $component
            )
        );

        outro(
            "View was created: " . str_replace(
                base_path(),
                '',
                $viewPath
            )
        );

        return self::SUCCESS;
    }
}
