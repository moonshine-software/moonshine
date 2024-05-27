<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

#[AsCommand(name: 'moonshine:apply')]
class MakeApplyCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:apply {className?}';

    protected $description = 'Create apply for Field';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $apply = $this->getDirectory() . "/Applies/$className.php";

        if(! is_dir($this->getDirectory() . '/Applies')) {
            $this->makeDir($this->getDirectory() . '/Applies');
        }

        $this->copyStub('Apply', $apply, [
            '{namespace}' => moonshineConfig()->getNamespace('\Applies'),
            'DummyClass' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $apply
            )
        );

        return self::SUCCESS;
    }
}
