<?php

declare(strict_types=1);

namespace MoonShine;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\InvalidHome;
use MoonShine\MenuManager\MenuFiller;
use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use MoonShine\Resources\Resources;
use MoonShine\Support\MemoizeRepository;
use Throwable;

class MoonShine
{
    use Conditionable;

    final public const DIR = 'app/MoonShine';

    final public const NAMESPACE = 'App\MoonShine';

    private array $resources = [];

    private array $pages = [];

    private array $authorization = [];

    private string|Closure|null $homeClass = null;

    public function flushState(): void
    {
        $this->getResources()->transform(function (ResourceContract $resource): ResourceContract {
            $resource->flushState();

            return $resource;
        });

        $this->getPages()->transform(function (Page $page): Page {
            $page->flushState();

            return $page;
        });

        moonshineCache()->flush();
        moonshineRouter()->flushState();

        MemoizeRepository::getInstance()->flush();
    }

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : $path)
        );
    }

    public static function dir(string $path = ''): string
    {
        return (config('moonshine.dir') ?? static::DIR) . $path;
    }

    public static function namespace(string $path = ''): string
    {
        return (config('moonshine.namespace') ?? static::NAMESPACE) . $path;
    }

    /**
     * Register resources in the system
     *
     * @param  list<class-string<ResourceContract>>  $data
     */
    public function resources(array $data, bool $newCollection = false): self
    {
        if ($newCollection) {
            $this->resources = [];
        }

        $this->resources = array_merge(
            $this->resources,
            $data
        );

        return $this;
    }

    /**
     * Get collection of registered resources
     *
     * @return Resources<int, ResourceContract>
     */
    public function getResources(): Resources
    {
        return Resources::make($this->resources)
            ->map(fn (string|ResourceContract $class) => is_string($class) ? app($class) : $class);
    }

    /**
     * Register pages in the system
     *
     * @param  list<class-string<Page>>  $data
     */
    public function pages(array $data, bool $newCollection = false): self
    {
        if ($newCollection) {
            $this->pages = [];
        }

        $this->pages = array_merge(
            $this->pages,
            $data
        );

        return $this;
    }

    /**
     * Get collection of registered pages
     */
    public function getPages(): Pages
    {
        return Pages::make($this->pages)
            ->map(fn (string|Page $class) => is_string($class) ? app($class) : $class);
    }


    public function init(): self
    {
        return $this;
    }

    /**
     * @param  class-string<Page>  $default
     */
    public function getPageFromConfig(string $pageName, string $default): Page
    {
        $class = config("moonshine.pages.$pageName", $default);

        return app($class);
    }

    public function configureRoutes(): array
    {
        return array_filter([
            'domain' => config('moonshine.route.domain', ''),
            'prefix' => config('moonshine.route.prefix', ''),
            'middleware' => 'moonshine',
            'as' => 'moonshine.',
        ]);
    }

    public function authorizationRules(): Collection
    {
        return collect($this->authorization);
    }

    public function defineAuthorization(Closure $rule): void
    {
        $this->authorization[] = $rule;
    }

    /**
     * Set home page/resource when visiting the base MoonShine url.
     *
     * @param  class-string<Page|ResourceContract>|Closure  $homeClass
     */
    public function home(string|Closure $homeClass): void
    {
        $this->homeClass = $homeClass;
    }

    /**
     * @throws Throwable
     * @throws InvalidHome
     */
    public function homeUrl(): ?string
    {
        if ($class = value($this->homeClass)) {
            throw_unless(is_a($class, MenuFiller::class, true), InvalidHome::create($class));

            return (new $class())->url();
        }

        return null;
    }
}
