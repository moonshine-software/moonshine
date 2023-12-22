<?php

declare(strict_types=1);

namespace MoonShine;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\InvalidHome;
use MoonShine\Menu\MenuElement;
use MoonShine\Menu\MenuGroup;
use MoonShine\Menu\MenuItem;
use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use MoonShine\Pages\ProfilePage;
use Throwable;

class MoonShine
{
    final public const DIR = 'app/MoonShine';

    final public const NAMESPACE = 'App\MoonShine';

    protected ?Collection $resources = null;

    protected ?Pages $pages = null;

    protected ?Collection $menu = null;

    protected ?Collection $vendorsMenu = null;

    protected array $authorization = [];

    protected string|Closure|null $homeClass = null;

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

    public function getResourceFromClassName(string $className): ?ResourceContract
    {
        return $this->getResources()
            ->first(
                fn (ResourceContract $resource): bool => $resource::class === $className
            );
    }

    public function getResourceFromUriKey(?string $uri): ?ResourceContract
    {
        if (is_null($uri)) {
            return null;
        }

        return $this->getResources()
            ->first(
                fn (ResourceContract $resource): bool => $resource->uriKey() === $uri
            );
    }

    public function getPageFromUriKey(?string $uri): ?Page
    {
        if (is_null($uri)) {
            return null;
        }

        return $this->getPages()->findByUri($uri);
    }

    /**
     * Register resources in the system
     *
     * @param  array<ResourceContract>  $data
     */
    public function resources(array $data, bool $newCollection = false): self
    {
        $this->resources = $newCollection
            ? collect($data)
            : $this->getResources()->merge($data);

        return $this;
    }

    /**
     * Get collection of registered resources
     *
     * @return Collection<int, ResourceContract>
     */
    public function getResources(): Collection
    {
        return $this->resources ?? collect();
    }

    /**
     * Register pages in the system
     *
     * @param  array<Page>  $data
     */
    public function pages(array $data, bool $newCollection = false): self
    {
        $this->pages = $newCollection
            ? Pages::make($data)
            : $this->getPages()->merge($data);

        return $this;
    }

    /**
     * Get collection of registered pages
     */
    public function getPages(): Pages
    {
        return $this->pages ?? Pages::make();
    }

    /**
     * Get custom menu items for automatic registration
     * @return Collection<int, MenuElement>
     */
    public function getVendorsMenu(): Collection
    {
        return $this->vendorsMenu ?? collect();
    }

    /**
     * Set custom menu items to register them automatically later.
     * @param  array<MenuElement> $data
     */
    public function vendorsMenu(array $data): self
    {
        $this->vendorsMenu = $this->getVendorsMenu()->merge($data);

        return $this;
    }

    /**
     * Get collection of registered menu
     *
     * @return Collection<int, MenuElement>
     */
    public function getMenu(): Collection
    {
        return $this->menu ?? collect();
    }

    /**
     * Register Menu with resources and pages in the system
     *
     * @param  Closure|array<MenuElement>  $data
     */
    public function init(array|Closure $data, bool $newCollection = false): self
    {
        $this->pages = $this->getPages();
        $this->resources = $this->getResources();

        if (! is_closure($data)) {
            $this->menu = $newCollection ? collect() : $this->getMenu();

            collect($data)->merge($this->getVendorsMenu())->each(
                function (MenuElement $item): void {
                    $this->menu->add($item);
                    $this->resolveMenuItem($item);
                }
            );
        }

        if (! empty(config('moonshine.pages.profile')) && config('moonshine.auth.enable', true)) {
            $this->pages->add(
                new (config('moonshine.pages.profile', ProfilePage::class))()
            );
        }

        if (class_exists(config('moonshine.pages.dashboard'))) {
            $this->pages->add(new (config('moonshine.pages.dashboard'))());
        }

        moonshineMenu()
            ->register(is_closure($data) ? $data : $this->menu);

        return $this->resolveRoutes();
    }

    private function resolveMenuItem(MenuElement $element): void
    {
        if ($element instanceof MenuGroup) {
            $element->items()->each(
                fn (MenuElement $item) => $this->resolveMenuItem($item)
            );
        } elseif ($element->isItem()) {
            $filler = $element instanceof MenuItem
                ? $element->getFiller()
                : null;

            if ($filler instanceof Page) {
                $this->pages->add($filler);
            }

            if ($filler instanceof ResourceContract) {
                $this->resources->add($filler);
            }
        }
    }

    /**
     * Register moonshine routes and resources routes in the system
     */
    public function resolveRoutes(): self
    {
        Route::group($this->configureRoutes(), function (): void {
            $this->getResources()->each(
                static function (ResourceContract $resource): void {
                    $resource->routes();
                }
            );
        });

        return $this;
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
