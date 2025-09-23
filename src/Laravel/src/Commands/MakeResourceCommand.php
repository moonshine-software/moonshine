<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\{confirm, outro, select, text};

use MoonShine\Laravel\Support\StubsPath;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:resource')]
class MakeResourceCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:resource {className?} {--type=} {--m|model=} {--t|title=} {--test} {--pest} {--p|policy} {--base-dir=} {--base-namespace=}';

    protected $description = 'Create resource';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Resource name',
            'ArticleResource',
            required: true,
        );

        $className = str($className)
            ->ucfirst()
            ->remove('resource', false)
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $className);
        $title = $this->option('title') ?? str($className)->singular()->plural()->value();

        $stubsPath = new StubsPath("{$className}Resource", 'php');
        $name = str($stubsPath->name)
            ->remove('resource', false)
            ->value();

        $stubsPath = $this->qualifyStubsDir($stubsPath, 'Resources');

        $this->makeDir($stubsPath->dir);

        $types = [
            'ModelResourceDefault' => 'Default model resource',
            'ModelResourceWithPages' => 'Model resource with pages',
            'Resource' => 'Empty resource',
        ];

        if ($type = $this->option('type')) {
            $keys = array_keys($types);
            $stub = $keys[$type - 1] ?? $keys[0];
        } else {
            $stub = select('Resource type', $types, 'ModelResourceDefault');
        }
        if (file_exists($stubsPath->getPath()) && ! confirm('File ' . $stubsPath->getPath() . ' exists, override?', false)) {
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

            outro('Test was created: ' . $this->getRelativePath($testPath));
        }

        if ($stub === 'ModelResourceWithPages') {
            $this->call(MakePageCommand::class, [
                'className' => $name,
                '--crud' => true,
                '--without-register' => true,
            ]);

            $pageNamespace = $this->getNamespace("\Pages\\$name\\$name");

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
            ]);
        }

        return self::SUCCESS;
    }
}
