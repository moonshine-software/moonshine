<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Closure;
use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\{confirm, info, multiselect};

use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Resources\MoonShineUserResource;
use MoonShine\Laravel\Resources\MoonShineUserRoleResource;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:publish')]
class PublishCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:publish {type?}';

    public function handle(): int
    {
        $types = $this->argument('type')
            ? [$this->argument('type')]
            : multiselect(
                'Types',
                [
                    'assets' => 'Assets',
                    'assets-template' => 'Assets template',
                    'resources' => 'System Resources (MoonShineUserResource, MoonShineUserRoleResource)',
                    'forms' => 'System Forms (LoginFrom, FiltersForm)',
                    'pages' => 'System Pages (ProfilePage, LoginPage, ErrorPage)',
                ],
                required: true,
            );

        if (\in_array('assets', $types, true)) {
            $this->call('vendor:publish', [
                '--tag' => 'moonshine-assets',
                '--force' => true,
            ]);
        }

        if (\in_array('assets-template', $types, true)) {
            $this->publishAssetsTemplate();
        }

        if (\in_array('resources', $types, true)) {
            $this->publishResources();
        }

        if (\in_array('forms', $types, true)) {
            $this->publishForms();
        }

        if (\in_array('pages', $types, true)) {
            $this->publishPages();
        }

        return self::SUCCESS;
    }

    private function publishAssetsTemplate(): void
    {
        $this->copyStub(
            'assets/css',
            resource_path('css/app.css'),
        );

        $this->copyStub(
            'assets/vite.config',
            base_path('vite.config.js'),
        );

        $this->copyStub(
            'assets/postcss.config.preset',
            base_path('postcss.config.js'),
        );

        if (confirm('Install modules automatically? (tailwindcss, @tailwindcss/postcss, @tailwindcss/vite, tom-select)')) {
            $this->flushNodeModules();

            self::updateNodePackages(static fn ($packages): array => [
                 'tailwindcss' => '^4',
                 '@tailwindcss/postcss' => '^4',
                 '@tailwindcss/vite' => '^4',
                 'tom-select' => '^2.4.3',
             ] + $packages);

            $this->installNodePackages();

            info('Node packages installed');
        }

        info('app.css, vite.config.js, postcss.config.js published');

        $this->line('Use in blade');
        info(<<<HTML
            @vite(['resources/js/app.js'], 'vendor/moonshine')
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        HTML);

        $this->line('Use in MoonShineLayout');
        info(<<<PHP
            protected function assets(): array
            {
                return [
                    \$this->getMainThemeJs(),
                    Css::make(Vite::asset('resources/css/app.css')),
                    Js::make(Vite::asset('resources/js/app.js')),
                ];
            }
        PHP);
    }

    private function publishResources(): void
    {
        $this->publishSystemResource('MoonShineUserResource', 'MoonshineUser');
        $this->publishSystemResource('MoonShineUserRoleResource', 'MoonshineUserRole');

        info('Resources published');
    }

    private function publishSystemResource(string $name, string $model): void
    {
        $dir = str_replace('Resource', '', $name);

        $copyInfo = $this->copySystemClass($name, "Resources", "Resources/$dir");
        $fullClassPath = $copyInfo['full_class_path'];
        $targetNamespace = $copyInfo['target_namespace'];

        $this->replaceInFile(
            "use MoonShine\Laravel\Models\\$model;",
            "use MoonShine\Laravel\Models\\$model;\nuse MoonShine\Laravel\Resources\ModelResource;",
            $fullClassPath,
        );

        $this->replaceInFile(
            "use MoonShine\Laravel\Pages\\$dir",
            "use App\MoonShine\Resources\\$dir\Pages",
            $fullClassPath,
        );

        $this->replaceInFile(
            "use MoonShine\Laravel\Resources\\$name;",
            "use $targetNamespace\\$name;",
            app_path('Providers/MoonShineServiceProvider.php'),
        );

        $provider = file_get_contents(app_path('Providers/MoonShineServiceProvider.php'));

        if (! str_contains($provider, "$targetNamespace\\$name")) {
            self::addResourceOrPageToProviderFile($name, namespace: $targetNamespace);
        }

        $replaceResources = function (string $fullClassPath): void {
            $this->replaceInFile(
                MoonShineUserResource::class,
                "App\MoonShine\Resources\MoonShineUser\MoonShineUserResource",
                $fullClassPath,
            );

            $this->replaceInFile(
                MoonShineUserRoleResource::class,
                "App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource",
                $fullClassPath,
            );
        };

        $this->copySystemClass("{$dir}IndexPage", "Pages/$dir", "Resources/$dir/Pages", then: $replaceResources);
        $this->copySystemClass("{$dir}FormPage", "Pages/$dir", "Resources/$dir/Pages", then: $replaceResources);
    }

    private function publishForms(): void
    {
        $formTypes = multiselect(
            'Forms',
            [
                'login' => 'LoginForm',
                'filters' => 'FiltersForm',
            ],
            required: true,
        );

        if (\in_array('login', $formTypes, true)) {
            $this->publishSystemForm('LoginForm', 'login');
        }

        if (\in_array('filters', $formTypes, true)) {
            $this->publishSystemForm('FiltersForm', 'filters');
        }

        info('Forms published');
    }

    private function publishSystemForm(string $className, string $configKey): void
    {
        $this->makeDir($this->getDirectory('/Forms'));

        $this->copySystemClass(
            $className,
            'Forms',
            overrideDir: MoonShine::crudPath('/Forms'),
            overrideNamespace: 'MoonShine\Crud\Forms',
        );

        $this->replaceInConfig(
            $configKey,
            $this->getNamespace('\Forms\\' . $className) . "::class",
            $className,
        );
    }

    private function publishPages(): void
    {
        $pageTypes = multiselect(
            'Pages',
            [
                'profile' => 'ProfilePage',
                'login' => 'LoginPage',
                'error' => 'ErrorPage',
            ],
            required: true,
        );

        if (\in_array('profile', $pageTypes, true)) {
            $this->publishSystemPage('ProfilePage', 'profile');
        }

        if (\in_array('login', $pageTypes, true)) {
            $this->publishSystemPage('LoginPage', 'login');
        }

        if (\in_array('error', $pageTypes, true)) {
            $this->publishSystemPage('ErrorPage', 'error');
        }

        info('Pages published');
    }

    private function publishSystemPage(string $className, string $configKey): void
    {
        $this->makeDir($this->getDirectory('/Pages'));

        $copyInfo = $this->copySystemClass($className, 'Pages');

        $this->replaceInFile(
            "namespace {$copyInfo['target_namespace']};\n",
            "namespace {$copyInfo['target_namespace']};\n\nuse MoonShine\Laravel\Pages\Page;",
            $copyInfo['full_class_path'],
        );

        $this->replaceInConfig(
            $configKey,
            $this->getNamespace('\Pages\\' . $className) . "::class",
            $className,
        );
    }

    /**
     * @return array{full_class_path: string, target_namespace: string}
     */
    private function copySystemClass(
        string $name,
        string $dir,
        ?string $outputDir = null,
        ?Closure $then = null,
        ?string $overrideDir = null,
        ?string $overrideNamespace = null,
    ): array {
        $outputDir ??= $dir;

        $targetClassPath = ($overrideDir ?: MoonShine::path("src/$dir")) . "/$name.php";
        $outputClassPath = $this->getDirectory("/$outputDir/$name.php");

        $targetNamespace = $overrideNamespace ?: str_replace('/', '\\', $dir);
        $tmpNamespace = str_replace('/', '\\', $outputDir);
        $outputNamespace = $this->getNamespace("\\$tmpNamespace");

        (new Filesystem())->makeDirectory($this->getDirectory($outputDir), recursive: true, force: true);
        (new Filesystem())->put(
            $outputClassPath,
            file_get_contents($targetClassPath),
        );

        $this->replaceInFile(
            $overrideNamespace ? "namespace $overrideNamespace;" : "namespace MoonShine\Laravel\\$targetNamespace;",
            "namespace $outputNamespace;",
            $outputClassPath,
        );

        if ($then instanceof Closure) {
            $then($outputClassPath, $outputNamespace);
        }

        return [
            'full_class_path' => $outputClassPath,
            'target_namespace' => $outputNamespace,
        ];
    }
}
