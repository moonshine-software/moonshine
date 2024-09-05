<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\{outro, text};

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:handler')]
class MakeHandlerCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:handler {className?}';

    protected $description = 'Create handler class';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $path = $this->getDirectory() . "/Handlers/$className.php";

        if (! is_dir($this->getDirectory() . '/Handlers')) {
            $this->makeDir($this->getDirectory() . '/Handlers');
        }

        $this->copyStub('Handler', $path, [
            '{namespace}' => moonshineConfig()->getNamespace('\Handlers'),
            'DummyHandler' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $path
            )
        );

        return self::SUCCESS;
    }
}
