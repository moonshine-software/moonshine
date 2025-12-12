<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use JsonException;

use function Laravel\Prompts\{outro, text};

use Leeto\PackageCommand\Command;
use MoonShine\Laravel\Support\StubsPath;
use MoonShine\MenuManager\MenuItem;
use ReflectionClass;

abstract class MoonShineCommand extends Command
{
    protected string $stubsDir = __DIR__ . '/../../stubs';

    protected static array $jsonOutput = [];

    protected function getDirectory(string $path = '', ?string $base = null): string
    {
        return moonshineConfig()->getDir($path, $base);
    }

    protected function getNamespace(string $path = '', ?string $base = null): string
    {
        return moonshineConfig()->getNamespace($path, $base);
    }

    protected function getRelativePath(string $path): string
    {
        return str_replace(base_path(), '', $path);
    }

    public static function addResourceOrPageToProviderFile(
        string $class,
        bool $page = false,
        string $namespace = '',
    ): void {
        $method = $page ? 'pages' : 'resources';
        $namespace = rtrim($namespace, '\\');

        self::addResourceOrPageTo(
            class: "$namespace\\$class",
            to: app_path('Providers/MoonShineServiceProvider.php'),
            between: static fn (Stringable $content): Stringable => $content->betweenFirst("->$method([", ']'),
            replace: static function (Stringable $content, Closure $tab) use ($class): Stringable {
                $prefixTab = 1;

                if (! $content->rtrim()->endsWith(',')) {
                    $content = $content->rtrim()->append(",\n");
                    $prefixTab = 4;
                }

                return $content->append("{$tab($prefixTab)}$class::class,\n{$tab(3)}");
            },
        );
    }

    public static function addResourceOrPageToMenu(string $class, string $title, string $namespace = ''): void
    {
        $namespace = rtrim($namespace, '\\');

        $reflector = new ReflectionClass(moonshineConfig()->getLayout());

        self::addResourceOrPageTo(
            class: "$namespace\\$class",
            to: $reflector->getFileName(),
            between: static fn (Stringable $content): Stringable => $content->betweenFirst(
                "protected function menu(): array",
                '}',
            ),
            replace: static function (Stringable $content, Closure $tab) use ($title, $class): Stringable {
                if (! $content->rtrim()->squish()->remove(' ')->endsWith('),];')) {
                    $lines = explode("\n", $content->value());

                    foreach ($lines as $index => $line) {
                        $line = rtrim($line);
                        if ((preg_match('/\)]$/', $line) || preg_match('/\)$/', $line)) && ! str_ends_with(
                            $line,
                            ',',
                        )) {
                            $lines[$index] = $line . ',';
                        }
                    }

                    $content = Str::of(implode("\n", $lines));
                }

                return $content->replace("];", "{$tab()}MenuItem::make($class::class, '$title'),\n{$tab(2)}];");
            },
            use: MenuItem::class,
        );
    }

    /**
     * @param  Closure(Stringable $content): Stringable  $between
     * @param  Closure(Stringable $content, Closure $tab): Stringable  $replace
     */
    private static function addResourceOrPageTo(
        string $class,
        string $to,
        Closure $between,
        Closure $replace,
        string $use = '',
    ): void {
        if (! file_exists($to)) {
            return;
        }

        $basename = class_basename($class);
        $namespace = $class;

        $content = Str::of(file_get_contents($to));

        if ($content->contains('->autoload(') || $content->contains('->autoloadMenu(')) {
            return;
        }

        if ($content->contains(['\\' . $basename . ';', '\\' . $basename . ','])) {
            return;
        }

        $tab = static fn (int $times = 1): string => str_repeat(' ', $times * 4);

        $headSection = $content->before('class ');
        $replaceContent = $between($content);

        if ($content->contains($use)) {
            $use = '';
        }

        $content = str_replace(
            [
                $headSection->value(),
                $replaceContent->value(),
            ],
            [
                $headSection->replaceLast(';', (";\nuse $namespace;" . ($use ? "\nuse $use;" : '')))->value(),
                $replace($replaceContent, $tab)->value(),
            ],
            $content->value(),
        );

        file_put_contents($to, $content);
    }

    protected function replaceInConfig(
        string $key,
        string $value,
        ?string $classReplace = null,
    ): void {
        $replace = "'$key' => $value,";

        $pattern = \is_null($classReplace) ?
            "/['\"]" . $key . "['\"]\s*=>\s*[^'\"]+?,/"
            : "/['\"]" . $key . "['\"]\s*=>\s*" . $classReplace . "::class,/";

        file_put_contents(
            config_path('moonshine.php'),
            preg_replace([
                $pattern,
            ], $replace, file_get_contents(config_path('moonshine.php'))),
        );
    }

    protected function makeViewFromStub(string $path, string $name, string $dir = ''): string
    {
        $suggestView = Str::of($name)
            ->classBasename()
            ->kebab()
            ->prepend(
                $path . '.' . Str::of($dir)
                    ->replace('/', '.')
                    ->lower()
                    ->whenNotEmpty(fn (Stringable $str) => $str->append('.')),
            )
            ->value();

        $view = $this->option('view') ?? text(
            'Path to view',
            $suggestView,
            default: $suggestView,
            required: true,
        );

        $view = str_replace('.blade.php', '', $view);
        $viewPath = resource_path('views/' . str_replace('.', DIRECTORY_SEPARATOR, $view));
        $viewPath .= '.blade.php';

        $this->makeDir(
            \dirname($viewPath),
        );

        $this->copyStub('view', $viewPath);

        outro(
            "View was created: " . $this->getRelativePath($viewPath),
        );

        return $view;
    }

    protected function fastCreateFromStub(string $stub, string $dir): void
    {
        $className = $this->argument('className') ?? text(
            'Class name',
            required: true,
        );

        $stubsPath = $this->qualifyStubsDir(new StubsPath($className, 'php'), $dir);

        $this->makeDir($stubsPath->dir);

        $this->copyStub($stub, $stubsPath->getPath(), [
            '{namespace}' => $stubsPath->namespace,
            'DummyClass' => $stubsPath->name,
        ]);

        $this->wasCreatedInfo($stubsPath);
    }

    protected function qualifyStubsDir(StubsPath $stubsPath, string $dir, ?string $namespace = null): StubsPath
    {
        $baseDir = $this->hasOption('base-dir') ? $this->option('base-dir') : null;
        $baseNamespace = $this->hasOption('base-namespace') ? $this->option('base-namespace') : null;

        $toNamespace = static fn (string $str): string
            => Str::of($str)
            ->trim('\\')
            ->trim('/')
            ->replace('/', '\\')
            ->explode('\\')
            ->map(static fn (string $segment): string => ucfirst($segment))
            ->implode('\\');

        if ($baseDir !== null && $baseNamespace === null) {
            $baseNamespace = $toNamespace($baseDir);
        }

        if ($namespace === null) {
            $namespace = $toNamespace($dir);
        }

        $baseDir = $baseDir ? trim($baseDir, '/') : $baseDir;
        $baseNamespace = $baseNamespace ? $toNamespace($baseNamespace) : $baseNamespace;

        return $stubsPath->prependDir(
            $this->getDirectory($dir, $baseDir),
        )->prependNamespace(
            $this->getNamespace($namespace, $baseNamespace),
        );
    }

    protected function wasCreatedInfo(StubsPath|string $stubsPath): void
    {
        $path = $this->getRelativePath(
            $stubsPath instanceof StubsPath ? $stubsPath->getPath() : $stubsPath,
        );

        $name = $stubsPath instanceof StubsPath ? $stubsPath->name : class_basename($stubsPath);

        if ($this->hasOption('json') && $this->option('json')) {
            static::$jsonOutput[] = [
                'name' => $name,
                'path' => $path,
                'status' => 'created',
            ];
        } else {
            outro("$name was created: $path");
        }
    }

    /**
     * @throws JsonException
     */
    protected function formatOutput(): void
    {
        if ($this->hasOption('without-output') && $this->option('without-output')) {
            return;
        }

        if ($this->hasOption('json') && $this->option('json')) {
            $this->line(
                json_encode(
                    static::$jsonOutput,
                    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                ),
            );

            static::$jsonOutput = [];
        }
    }
}
