<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Illuminate\Support\Collection;
use MoonShine\Exceptions\DecorationException;
use Throwable;

/**
 * @method static static make(array $tabs = [])
 */
class Tabs extends Decoration
{
    protected string $view = 'moonshine::decorations.tabs';

    protected string|int|null $active = null;

    public function __construct(protected array $tabs = [])
    {
        parent::__construct(uniqid('', true));
    }

    public function active(string|int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): string|int|null
    {
        return $this->active;
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

    public function contentWithHtml(): Collection
    {
        return $this->tabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->id() => view('moonshine::components.fields-group', [
                'components' => $tab->getFields(),
            ])->render(),
        ]);
    }
}
