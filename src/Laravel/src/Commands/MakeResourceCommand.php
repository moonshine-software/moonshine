<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use JsonException;

use function Laravel\Prompts\{confirm, select, text};

use MoonShine\Laravel\Support\StubsPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:resource')]
class MakeResourceCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:resource {className?} {--type=} {--m|model=} {--t|title=} {--test} {--pest} {--force} {--p|policy} {--base-dir=} {--base-namespace=} {--json}';

    protected $description = 'Create resource';

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Resource name',
            'ArticleResource',
            required: true,
        );

        $className = Str::of($className)
            ->ucfirst()
            ->remove('resource', false)
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $className);
        $title = $this->option('title') ?? Str::of($className)->singular()->plural()->value();
        $force = $this->option('force') ?? false;

        $stubsPath = new StubsPath("{$className}Resource", 'php');
        $name = Str::of($stubsPath->name)
            ->remove('resource', false)
            ->value();

        $stubsPath = $this->qualifyStubsDir($stubsPath, "Resources/$name");

        $this->makeDir($stubsPath->dir);

        $types = [
            'ModelResource' => 'Resource bound to an Eloquent model',
            'CrudResource' => 'Resource for custom data with your own logic',
            'Resource' => 'Blank resource, fully manual implementation',
        ];

        if ($type = $this->option('type')) {
            $keys = array_keys($types);
            $stub = $keys[$type - 1] ?? $keys[0];
        } else {
            $stub = select('Type', $types, 'ModelResource');
        }

        if (! $force && file_exists($stubsPath->getPath()) && ! confirm('File ' . $stubsPath->getPath() . ' exists, override?', false)) {
            return self::SUCCESS;
        }

        $properties = '';

        if ($this->option('policy')) {
            $properties .= PHP_EOL . str_repeat(' ', 4) . 'protected bool $withPolicy = true;' . PHP_EOL;
        }

        $replace = [
            '{namespace}' => $stubsPath->namespace,
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            '{properties}' => $properties,
            'DummyTitle' => $title,
            'DummyClass' => $stubsPath->name,
            'DummyResource' => $stubsPath->name,
        ];

        if ($this->option('test') || $this->option('pest')) {
            $testStub = $this->option('pest') ? 'pest' : 'test';
            $testPath = base_path("tests/Feature/{$stubsPath->name}Test.php");

            $this->copyStub($testStub, $testPath, $replace);

            $this->wasCreatedInfo($testPath);
        }

        if ($stub !== 'Resource') {
            $this->call(MakePageCommand::class, [
                'className' => $name,
                '--resource' => str_replace('::class', '', $stubsPath->getClassString()),
                '--crud' => true,
                '--force' => $force,
                '--json' => $this->option('json'),
                '--without-register' => true,
                '--without-output' => true,
            ]);

            $pageNamespace = $this->getNamespace("\Resources\\$name\Pages\\$name");

            $replace += [
                '{indexPage}' => "{$name}IndexPage",
                '{formPage}' => "{$name}FormPage",
                '{detailPage}' => "{$name}DetailPage",
                '{index-page-namespace}' => "{$pageNamespace}IndexPage",
                '{form-page-namespace}' => "{$pageNamespace}FormPage",
                '{detail-page-namespace}' => "{$pageNamespace}DetailPage",
            ];
        }

        $this->copyStub($stub, $stubsPath->getPath(), $replace);

        $this->wasCreatedInfo($stubsPath);

        self::addResourceOrPageToProviderFile(
            $stubsPath->name,
            namespace: $stubsPath->namespace
        );

        self::addResourceOrPageToMenu(
            $stubsPath->name,
            $title,
            namespace: $stubsPath->namespace
        );

        if ($this->option('policy')) {
            $this->call(MakePolicyCommand::class, [
                'className' => class_basename($model),
                '--json' => $this->option('json'),
                '--without-output' => true,
            ]);
        }

        $this->formatOutput();

        return self::SUCCESS;
    }
}
