<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\Action;
use MoonShine\Actions\MassActions;
use MoonShine\Contracts\Renderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Filters\Filter;
use MoonShine\Filters\Filters;

interface ResourceContract
{
    /**
     * Get a resource title, will be displayed in admin panel menu
     */
    public function title(): string;

    /**
     * Define a field name, which will be used to display value in relation
     */
    public function titleField(): string;

    /**
     * Define if the resources protected by authentication
     */
    public function isWithPolicy(): bool;

    /**
     * Get a model class, related to resource
     */
    public function getModel(): Model;

    /**
     * Get current eloquent instance
     */
    public function getItem(): ?Model;

    /**
     * Get a collection of additional actions performed on resource page
     *
     * @return MassActions<Action>
     */
    public function getActions(): MassActions;

    /**
     * Get a collection of fields of related model
     *
     * @return Fields<Field>
     */
    public function getFields(): Fields;

    /**
     * Get a collection of filters
     *
     * @return Filters<Filter>
     */
    public function getFilters(): Filters;

    /**
     * Check whether user can perform action on model
     *
     * @param  string  $ability  view, viewAny, restore, forceDelete
     */
    public function can(string $ability): bool;

    public function uriKey(): string;

    public function resolveQuery(): Builder;

    public function resolveRoutes(): void;

    public function renderComponent(
        Renderable $component,
        Model $item,
        int $level = 0
    ): View;
}
