<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Illuminate\Support\Collection;
use MoonShine\Exceptions\DecorationException;
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
                $tabs->every(fn ($tab) => ! $tab instanceof Tab),
                new DecorationException('Tabs must be a class of '.Tab::class)
            );
        });
    }
}
