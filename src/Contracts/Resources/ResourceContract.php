<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Actions\Actions;
use Leeto\MoonShine\Contracts\ResourceRenderable;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Filters\Filter;
use Leeto\MoonShine\Filters\Filters;

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
     * @param  Model|null  $item  Model on which the action is performed
     * @return bool
     */
    public function can(string $ability, Model $item = null): bool;

    public function uriKey(): string;

    public function resolveQuery(): Builder;

    public function resolveRoutes(): void;

    public function renderDecoration(ResourceRenderable $decoration, Model $item);

    public function renderField(ResourceRenderable $field, Model $item, int $level = 0);

    public function renderFilter(ResourceRenderable $field, Model $item);

    public function renderMetric(ResourceRenderable $metric);
}
