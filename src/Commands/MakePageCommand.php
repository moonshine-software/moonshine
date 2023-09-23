<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;

use function Laravel\Prompts\text;

use MoonShine\MoonShine;

class MakePageCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:page {className?} {--dir=}';

    protected $description = 'Create page';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $dir = $this->option('dir') ?? 'Pages';

        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $page = $this->getDirectory() . "/$dir/$className.php";

        if(! is_dir($this->getDirectory() . "/$dir")) {
            $this->makeDir($this->getDirectory() . "/$dir");
        }

        $this->copyStub('Page', $page, [
            '{namespace}' => MoonShine::namespace('\\' . str_replace('/', '\\', $dir)),
            'DummyPage' => $className,
            'DummyTitle' => $className,
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $page
            )
        );

        return self::SUCCESS;
    }
}
