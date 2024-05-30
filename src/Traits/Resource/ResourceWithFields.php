<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Illuminate\Support\Collection;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\FilterException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Support\Filters;
use Throwable;

trait ResourceWithFields
{
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [];
    }

    public function getFields(): Fields
    {
        return Fields::make($this->fields());
    }

    /**
     * @return list<Field>
     */
    public function indexFields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getIndexFields(): Fields
    {
        $fields = $this->getPages()
            ->findByType(PageType::INDEX)
            ?->fields();

        if (empty($fields)) {
            $fields = $this->indexFields()
                ?: $this->fields();
        }

        return Fields::make($fields)
            ->onlyFields(withWrappers: true)
            ->indexFields();
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function formFields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getFormFields(bool $withOutside = false): Fields
    {
        $fields = $this->getPages()
            ->findByType(PageType::FORM)
            ?->fields();

        if (empty($fields)) {
            $fields = $this->formFields()
                ?: $this->fields();
        }

        return Fields::make($fields)
            ->formFields(withOutside: $withOutside);
    }

    /**
     * @return list<Field>
     */
    public function detailFields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getDetailFields(bool $withOutside = false, bool $onlyOutside = false): Fields
    {
        $fields = $this->getPages()
            ->findByType(PageType::DETAIL)
            ?->fields();

        if (empty($fields)) {
            $fields = $this->detailFields()
                ?: $this->fields();
        }

        return Fields::make($fields)
            ->onlyFields(withWrappers: true)
            ->detailFields(withOutside: $withOutside, onlyOutside: $onlyOutside);
    }

    /**
     * @return Collection<int, ModelRelationField>
     * @throws Throwable
     */
    public function getOutsideFields(): Fields
    {
        $fields = $this->getPages()
            ->findByType(PageType::FORM)
            ?->fields();

        if (empty($fields)) {
            $fields = $this->formFields()
                ?: $this->fields();
        }

        return Fields::make($fields)
            ->onlyFields()
            ->onlyOutside();
    }

    public function exportFields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getExportFields(): Fields
    {
        $fields = $this->exportFields();

        if ($fields !== []) {
            return Fields::make($fields)
                ->ensure(Field::class);
        }

        $fields = $this->fields()
            ?: $this->indexFields();

        return Fields::make($fields)
            ->onlyFields()
            ->exportFields();
    }

    public function importFields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getImportFields(): Fields
    {
        $fields = $this->importFields();

        if ($fields !== []) {
            return Fields::make($fields)
                ->ensure(Field::class);
        }

        $fields = $this->fields()
            ?: $this->indexFields();

        return Fields::make($fields)
            ->onlyFields()
            ->importFields();
    }

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getFilters(): Fields
    {
        $filters = Fields::make($this->filters())
            ->withoutOutside()
            ->wrapNames('filters');

        $filters->each(function ($filter): void {
            if (in_array($filter::class, Filters::NO_FILTERS)) {
                throw new FilterException("You can't use " . $filter::class . " inside filters.");
            }
        });

        return $filters;
    }
}
