<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\{info, outro, select, text};

use MoonShine\MoonShine;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:resource')]
class MakeResourceCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:resource {name?} {--m|model=} {--t|title=} {--test} {--pest}';

    protected $description = 'Create resource';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $name = str(
            text(
                'Name',
                'ArticleResource',
                $this->argument('name') ?? '',
                required: true,
            )
        );

        $dir = $name->ucfirst()
            ->remove('Resource', false)
            ->value();

        $name = $name->ucfirst()
            ->replace(['resource', 'Resource'], '')
            ->value();

        $model = $this->qualifyModel($this->option('model') ?? $name);
        $title = $this->option('title') ?? str($name)->singular()->plural()->value();

        $resource = $this->getDirectory() . "/Resources/{$name}Resource.php";

        if (! is_dir($this->getDirectory() . "/Resources")) {
            $this->makeDir($this->getDirectory() . "/Resources");
        }

        $stub = select('Resource type', [
            'ModelResourceDefault' => 'Default model resource',
            'ModelResourceSeparate' => 'Separate model resource',
            'ModelResourceWithPages' => 'Model resource with pages',
            'Resource' => 'Empty resource',
        ], 'ModelResourceDefault');

        $replaceData = [
            '{namespace}' => MoonShine::namespace('\Resources'),
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            'DummyTitle' => $title,
            'Dummy' => $name,
        ];

        if($this->option('test') || $this->option('pest')) {
            $testStub = $this->option('pest') ? 'pest' : 'test';
            $testPath = base_path('tests/Feature/') . $name . 'ResourceTest.php';

            $this->copyStub($testStub, $testPath, $replaceData);

            info('Test file was created');
        }

        if ($stub === 'ModelResourceWithPages') {
            $pageDir = "Pages/$dir";

            $this->call(MakePageCommand::class, [
                'className' => $name,
                '--crud' => true,
            ]);

            $pageNamespace = static fn (string $name): string => MoonShine::namespace(
                str_replace('/', '\\', "\\$pageDir\\$dir$name")
            );

            $replaceData = [
                    '{indexPage}' => "{$dir}IndexPage",
                    '{formPage}' => "{$dir}FormPage",
                    '{detailPage}' => "{$dir}DetailPage",
                    '{index-page-namespace}' => $pageNamespace('IndexPage'),
                    '{form-page-namespace}' => $pageNamespace('FormPage'),
                    '{detail-page-namespace}' => $pageNamespace('DetailPage'),
                ] + $replaceData;
        }

        $this->copyStub($stub, $resource, $replaceData);

        info(
            "{$name}Resource file was created: " . str_replace(
                base_path(),
                '',
                $resource
            )
        );

        outro('Now register resource in menu');

        return self::SUCCESS;
    }
}
