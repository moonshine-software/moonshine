<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\MoonShineRequest;

final class MenuManager
{
    use Conditionable;

    private array $items = [];

    public function __construct(
        private MoonShineRequest $request
    )
    {
    }

    public function add(array|MenuElement $data): self
    {
        $this->items = array_merge(
            $this->items,
            is_array($data) ? $data : [$data]
        );

        return $this;
    }

    public function all(): Collection
    {
        return $this->prepareMenu($this->items);
    }

    public function hasForceActive(): bool
    {
        return $this->all()->contains(function (MenuElement $item) {
            if($item->isForceActive()) {
                return true;
            }

            if($item instanceof MenuGroup) {
                return $item->items()->contains(fn (MenuElement $child): bool => $child->isForceActive());
            }

            return false;
        });
    }

    public function prepareMenu(array $items = []): Collection
    {
        return collect($items)
            ->ensure(MenuElement::class)
            ->filter(function (MenuElement $item): bool {
                if ($item instanceof MenuGroup) {
                    $item->setItems(
                        $item->items()->filter(
                            fn (MenuElement $child): bool => $child->isSee(
                                $this->request
                            )
                        )
                    );
                }

                return $item->isSee(
                    $this->request
                );
        });
    }
}
