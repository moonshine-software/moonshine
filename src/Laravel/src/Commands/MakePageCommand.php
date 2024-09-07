<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:page')]
class MakePageCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:page {className?} {--force} {--without-register} {--crud} {--dir=} {--extends=}';

    protected $description = 'Create page';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $extends = $this->option('extends') ?? 'Page';

        $className = $this->argument('className') ?? text(
            'Class name',
            required: true
        );

        $dir = $this->option('dir') ?: dirname($className);
        $className = class_basename($className);

        if ($dir === '.') {
            $dir = 'Pages';
        }

        if (! $this->option('force') && ! $this->option('extends') && ! $this->option('crud')) {
            $types = [
                '' => 'Custom',
                'IndexPage' => 'IndexPage',
                'FormPage' => 'FormPage',
                'DetailPage' => 'DetailPage',
            ];

            $type = array_search(
                select('Type', $types),
                $types,
                true
            );

            $extends = $type ?: null;

            $this->makePage($className, $extends ? 'CrudPage' : 'Page', $dir, $extends);

            return self::SUCCESS;
        }

        if ($this->option('crud')) {
            $dir = "$dir/$className";
            foreach (['IndexPage', 'FormPage', 'DetailPage'] as $type) {
                $this->makePage($className . $type, 'CrudPage', $dir, $type);
            }

            return self::SUCCESS;
        }

        $this->makePage($className, 'Page', $dir, $extends);

        return self::SUCCESS;
    }

    /**
     * @throws FileNotFoundException
     */
    private function makePage(
        string $className,
        string $stub = 'Page',
        ?string $dir = null,
        ?string $extends = null
    ): void {
        $dir = is_null($dir) ? 'Pages' : $dir;
        $extends = $extends === null || $extends === '' || $extends === '0' ? 'Page' : $extends;

        $page = $this->getDirectory() . "/$dir/$className.php";

        if (! is_dir($this->getDirectory() . "/$dir")) {
            $this->makeDir($this->getDirectory() . "/$dir");
        }

        $this->copyStub($stub, $page, [
            '{namespace}' => moonshineConfig()->getNamespace('\\' . str_replace('/', '\\', $dir)),
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

        if(!$this->option('without-register')) {
            $prefix = str_contains($dir, 'Pages/')
                ? str_replace('Pages/', '', $dir) . '\\'
                : str_replace('Pages', '', $dir);

            self::addResourceOrPageToProviderFile(
                $className,
                page: true,
                prefix: $prefix
            );
        }
    }
}
