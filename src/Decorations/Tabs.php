<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Decorations;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Exceptions\DecorationException;
use Throwable;

class Tabs extends Decoration
{
    public static string $view = 'moonshine::decorations.tabs';

    public function __construct(protected array $tabs = [])
    {
        $this->setLabel(uniqid());
    }

    /**
     * @return Collection<Tab>
     * @throws Throwable
     */
    public function tabs(): Collection
    {
        return tap(Collection::make($this->tabs), function (Collection $tabs) {
            throw_if(
                $tabs->every(fn ($tab) => ! $tab instanceof Tab),
                new DecorationException('Tabs must be a class of '.Tab::class)
            );
        });
    }
}
