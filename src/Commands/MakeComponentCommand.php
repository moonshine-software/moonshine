<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use MoonShine\MoonShine;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

class MakeComponentCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:component {className?}';

    protected $description = 'Create component';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
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

        $this->copyStub('Component', $component, [
            '{namespace}' => MoonShine::namespace('\Components'),
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
    }
}
