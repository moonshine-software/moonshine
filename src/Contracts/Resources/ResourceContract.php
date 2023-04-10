<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Actions\Action;
use MoonShine\Actions\Actions;
use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Filters\Filter;
use MoonShine\Filters\Filters;

interface ResourceContract
{
    /**
     * Get a resource title, will be displayed in admin panel menu
     *
     * @return string
     */
    public function title(): string;

    /**
     * Define a field name, which will be used to display value in relation
     *
     * @return string
     */
    public function titleField(): string;

    /**
     * Define if the resources protected by authentication
     *
     * @return bool
     */
    public function isWithPolicy(): bool;

    /**
     * Get a model class, related to resource
     *
     * @return Model
     */
    public function getModel(): Model;

    /**
     * Get current eloquent instance
     *
     * @return ?Model
     */
    public function getItem(): ?Model;

    /**
     * Get a collection of additional actions performed on resource page
     *
     * @return Actions<Action>
     */
    public function getActions(): Actions;

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
     * @param  ?Model  $item  Model on which the action is performed
     * @return bool
     */
    public function can(string $ability, Model $item = null): bool;

    public function uriKey(): string;

    public function resolveQuery(): Builder;

    public function resolveRoutes(): void;

    public function renderComponent(
        ResourceRenderable $component,
        Model $item,
        int $level = 0
    ): View;
}
