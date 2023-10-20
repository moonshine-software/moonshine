<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\{outro, select, text};

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
        
        $extends = select('Extends', [
            'Page',
            'DetailPage',
            'FormPage',
            'IndexPage'
        ], 'Page');

        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        if(str($className)->contains('/')) {
            $dir.= str(dirname($className))
                ->start('/');
            $className = class_basename($className);
        }

        $page = $this->getDirectory() . "/$dir/$className.php";

        if(! is_dir($this->getDirectory() . "/$dir")) {
            $this->makeDir($this->getDirectory() . "/$dir");
        }

        $stub = $extends !== 'Page' ? 'CrudPage' : 'Page';

        $this->copyStub($stub, $page, [
            '{namespace}' => MoonShine::namespace('\\' . str_replace('/', '\\', $dir)),
            'DummyPage' => $className,
            'DummyTitle' => $className,
            '{extendShort}' => $extends,
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
