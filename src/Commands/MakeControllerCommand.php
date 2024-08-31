<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

use MoonShine\MoonShine;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:controller')]
class MakeControllerCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:controller {className?}';

    protected $description = 'Create controller';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $controller = $this->getDirectory() . "/Controllers/$className.php";

        if (! is_dir($this->getDirectory() . '/Controllers')) {
            $this->makeDir($this->getDirectory() . '/Controllers');
        }

        $this->copyStub('Controller', $controller, [
            '{namespace}' => MoonShine::namespace('\Controllers'),
            'DummyClass' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $controller
            )
        );

        return self::SUCCESS;
    }
}
