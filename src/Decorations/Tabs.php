<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use Throwable;
use MoonShine\Fields\Fields;
use MoonShine\Support\Condition;
use Illuminate\Support\Collection;
use MoonShine\Components\FieldsGroup;
use MoonShine\Exceptions\DecorationException;

/**
 * @method static static make(array $tabs = [])
 */
class Tabs extends Decoration
{
    protected string $view = 'moonshine::decorations.tabs';

    protected string|int|null $active = null;

    protected string $justifyAlign = 'start';

    protected bool $vertical = false;

    /**
     * @param list<Tab> $tabs
     */
    public function __construct(array $tabs = [])
    {
        parent::__construct(uniqid('', true), $tabs);
    }

    public function active(string|int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function justifyAlign(string $justifyAlign): self
    {
        $this->justifyAlign = $justifyAlign;

        return $this;
    }

    public function getJustifyAlign(): string
    {
        return $this->justifyAlign;
    }

    /**
     * @throws Throwable
     */
    public function getActive(): string|int|null
    {
        return $this->tabs()->firstWhere('active', true)?->id();
    }

    /**
     * @throws Throwable
     */
    public function tabsWithHtml(): Collection
    {
        return $this->tabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->id() => $tab->getIcon(6, 'secondary') . PHP_EOL
                . $tab->label(),
        ]);
    }

    /**
     * @return Fields<int, Tab>
     * @throws Throwable
     */
    public function tabs(): Fields
    {
        return tap(
            Fields::make($this->getRawFields()),
            static function (Fields $tabs): void {
                throw_if(
                    $tabs->every(fn ($tab): bool => ! $tab instanceof Tab),
                    new DecorationException(
                        'Tabs must be a class of ' . Tab::class
                    )
                );
            }
        );
    }

    public function getFields(): Fields
    {
        return $this->tabs();
    }

    /**
     * @throws Throwable
     */
    public function contentWithHtml(): Collection
    {
        return $this->tabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->id() => FieldsGroup::make(
                $tab->getFields()
            ),
        ]);
    }

    public function vertical(Closure|bool|null $condition = null): self
    {
        $this->vertical = Condition::boolean($condition, true);

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->vertical;
    }
}
