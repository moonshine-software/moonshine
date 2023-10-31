<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\{info, multiselect};

use MoonShine\MoonShine;

class PublishCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:publish';

    public function handle(): int
    {
        $types = multiselect(
            'Types',
            [
                'assets' => 'Assets',
                'layout' => 'Layout',
                'resources' => 'System Resources (MoonShineUserResource, MoonShineUserRoleResource)',
            ],
            required: true
        );

        if (in_array('assets', $types, true)) {
            $this->call('vendor:publish', [
                '--tag' => 'moonshine-assets',
            ]);
        }

        if (in_array('layout', $types, true)) {
            $this->copyStub(
                'Layout',
                MoonShine::dir('/MoonShineLayout.php'),
                [
                    '{namespace}' => MoonShine::namespace(),
                ]
            );

            $this->replaceInFile(
                'use MoonShine\MoonShineLayout;',
                'use App\MoonShine\MoonShineLayout;',
                config_path('moonshine.php')
            );

            info('Layout published');
        }

        if (in_array('resources', $types, true)) {
            $this->publishSystemResource('MoonShineUserResource', 'MoonshineUser');
            $this->publishSystemResource('MoonShineUserRoleResource', 'MoonshineUserRole');

            info('Resources published');
        }

        return self::SUCCESS;
    }

    private function publishSystemResource(string $name, string $model): void
    {
        $classPath = "/src/Resources/$name.php";
        $fullClassPath = MoonShine::dir("/Resources/$name.php");
        $targetNamespace = MoonShine::namespace('\Resources');

        (new Filesystem())->put(
            $fullClassPath,
            file_get_contents(MoonShine::path($classPath))
        );

        $this->replaceInFile(
            'namespace MoonShine\Resources;',
            "namespace $targetNamespace;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Models\\$model;",
            "use MoonShine\Models\\$model;\nuse MoonShine\Resources\ModelResource;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Resources\\$name;",
            "use $targetNamespace\\$name;",
            app_path('Providers/MoonShineServiceProvider.php')
        );
    }
}
