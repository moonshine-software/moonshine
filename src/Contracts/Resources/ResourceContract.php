<?php

namespace Leeto\MoonShine\Contracts\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Actions\Action;
use Leeto\MoonShine\Contracts\RenderableContract;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Fields\Field;

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
     * Get a model class, related to resource
     *
     * @return Model
     */
    public function getModel(): Model;

    /**
     * Get current eloquent instance
     *
     * @return Model
     */
    public function getItem(): Model;

    /**
     * Get a collection of additional actions performed on resource page
     *
     * @return Action[]
     */
    public function getActions(): Collection;

    /**
     * Define if the resources protected by authentication
     *
     * @return bool
     */
    public function isWithPolicy(): bool;

    /**
     * Get a collection of fields of related model
     *
     * @return Field[]
     */
    public function getFields(): Collection;

    /**
     * Get a collection of tabs, which will be displayed on create/update resource form
     *
     * @return Tab[]
     */
    public function tabs(): Collection;

    /**
     * Get a collection of fields of related model, which will be displayed on resource index page
     *
     * @return Field[]
     */
    public function indexFields(): Collection;

    /**
     * Get a collection of fields of related model, which will be exported
     *
     * @return Field[]
     */
    public function exportFields(): Collection;

    /**
     * Get an array of fields, which will be displayed on create/edit resource page
     *
     * @return Field[]
     */
    public function formFields(): Collection;

    /**
     * Get additional assets, which will be loaded on resource page
     *
     * @param string $type CSS or JS type
     * @return array
     */
    public function getAssets(string $type): array;

    public function extensions($name, Model $item): string;

    /**
     * Check whether user can perform action on model
     *
     * @param string $ability view, viewAny, restore, forceDelete
     * @param Model|null $item Model on which the action is performed
     * @return bool
     */
    public function can(string $ability, Model $item = null): bool;

    public function renderDecoration(RenderableContract $decoration, Model $item);

    public function renderField(RenderableContract $field, Model $item, int $level = 0);

    public function renderFilter(RenderableContract $field, Model $item);

    public function renderMetric(RenderableContract $metric);
}