<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

use MoonShine\MoonShine;
use Symfony\Component\Console\Attribute\AsCommand;

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
            '{namespace}' => MoonShine::namespace('\Applies'),
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
