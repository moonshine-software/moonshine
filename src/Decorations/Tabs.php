<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Exceptions\DecorationException;
use MoonShine\Resources\Resource;
use Throwable;

/**
 * @method static static make(array $tabs = [])
 */
class Tabs extends Decoration
{
    protected static string $view = 'moonshine::decorations.tabs';

    public function __construct(protected array $tabs = [])
    {
        parent::__construct(uniqid('', true));
    }

    /**
     * @throws Throwable
     */
    public function tabsWithHtml(): Collection
    {
        return $this->tabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->id() => $tab->getIcon(6, 'pink') . PHP_EOL
                . $tab->label(),
        ]);
    }

    /**
     * @return Collection<Tab>
     * @throws Throwable
     */
    public function tabs(): Collection
    {
        return tap(
            Collection::make($this->tabs),
            static function (Collection $tabs): void {
                throw_if(
                    $tabs->every(fn ($tab): bool => ! $tab instanceof Tab),
                    new DecorationException(
                        'Tabs must be a class of ' . Tab::class
                    )
                );
            }
        );
    }

    public function contentWithHtml(
        Resource $resource,
        ?Model $item = null
    ): Collection {
        return $this->tabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->id() => view('moonshine::components.resource-renderable', [
                'components' => $tab->getFields(),
                'item' => $item,
                'resource' => $resource,
            ])->render(),
        ]);
    }
}
