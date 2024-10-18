<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:layout')]
class MakeLayoutCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:layout {className?} {--compact} {--full} {--default} {--dir=}';

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

        $compact = ! $this->option('full') && ($this->option('compact') || confirm('Want to use a minimalist theme?'));

        $extendClassName = $compact ? 'CompactLayout' : 'AppLayout';
        $extends = "MoonShine\Laravel\Layouts\\$extendClassName";

        $this->copyStub('Layout', $layout, [
            '{namespace}' => moonshineConfig()->getNamespace('\\' . str_replace('/', '\\', $dir)),
            '{extend}' => $extends,
            '{extendShort}' => class_basename($extends),
            'DummyLayout' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $layout
            )
        );

        if ($this->option('default') || confirm('Use the default template in the system?')) {
            $current = config('moonshine.layout', 'AppLayout::class');
            $currentShort = class_basename($current);
            $replace = "'layout' => " . moonshineConfig()->getNamespace('\Layouts\\' . $className) . "::class";

            file_put_contents(
                config_path('moonshine.php'),
                str_replace([
                    "'layout' => $current::class",
                    "'layout' => $currentShort::class",
                ], $replace, file_get_contents(config_path('moonshine.php')))
            );
        }

        return self::SUCCESS;
    }
}
