<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Exceptions\DecorationException;
use MoonShine\Resources\Resource;
use Throwable;

class Tabs extends Decoration
{
    protected static string $view = 'moonshine::decorations.tabs';

    public function __construct(protected array $tabs = [])
    {
        parent::__construct(uniqid('', true));
    }

    /**
     * @return Collection<Tab>
     * @throws Throwable
     */
    public function tabs(): Collection
    {
        return tap(Collection::make($this->tabs), static function (Collection $tabs) {
            throw_if(
                $tabs->every(fn($tab) => !$tab instanceof Tab),
                new DecorationException('Tabs must be a class of '.Tab::class)
            );
        });
    }

    public function tabsWithHtml(): Collection
    {
        return $this->tabs()->mapWithKeys(function (Tab $tab) {
            return [
                $tab->id() => $tab->getIcon(6, 'pink') . PHP_EOL
                    . $tab->label()
            ];
        });
    }

    public function contentWithHtml(Resource $resource, ?Model $item = null): Collection
    {
        return $this->tabs()->mapWithKeys(function (Tab $tab) use ($resource, $item) {
            return [
                $tab->id() => view('moonshine::components.resource-renderable', [
                    'components' => $tab->getFields(),
                    'item' => $item,
                    'resource' => $resource,
                ])->render()
            ];
        });
    }
}
