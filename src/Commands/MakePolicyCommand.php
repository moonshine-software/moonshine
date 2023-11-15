<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use MoonShine\MoonShineAuth;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

class MakePolicyCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:policy {className?}';

    protected $description = 'Create policy for Model';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $className = $this->argument('className') ?? text(
            'Model',
            required: true
        );

        $model = $this->qualifyModel($className);
        $className = class_basename($model) . "Policy";

        $path = app_path("/Policies/$className.php");

        if(! is_dir(app_path('/Policies'))) {
            $this->makeDir(app_path('/Policies'));
        }

        $this->copyStub('Policy', $path, [
            'DummyClass' => $className,
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            '{user-model-namespace}' => MoonShineAuth::model()::class,
            '{user-model}' => class_basename(MoonShineAuth::model())
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
