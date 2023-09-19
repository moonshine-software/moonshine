<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\Exceptions\FilterException;
use MoonShine\Fields\Fields;
use MoonShine\Support\Filters;
use Throwable;

trait ResourceWithFields
{
    public function fields(): array
    {
        return [];
    }

    public function getFields(): Fields
    {
        return Fields::make($this->fields())->filter();
    }

    public function indexFields(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getIndexFields(): Fields
    {
        return Fields::make(
            empty($this->indexFields())
                ? $this->fields()
                : $this->indexFields()
        )->filter()->indexFields();
    }

    public function formFields(): array
    {
        return [];
    }

    public function getFormFields(): Fields
    {
        return Fields::make(
            empty($this->formFields())
                ? $this->fields()
                : $this->formFields()
        )->filter()->formFields()->withoutOutside();
    }

    public function getOutsideFields(): Fields
    {
        return Fields::make(
            empty($this->formFields())
                ? $this->fields()
                : $this->formFields()
        )->filter()->onlyOutside();
    }

    public function detailFields(): array
    {
        return [];
    }

    public function getDetailFields(): Fields
    {
        return Fields::make(
            empty($this->detailFields())
                ? $this->fields()
                : $this->detailFields()
        )->filter()->detailFields();
    }

    public function filters(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getFilters(): Fields
    {
        $filters = Fields::make($this->filters())
            ->filter()
            ->withoutOutside()
            ->wrapNames('filters');

        $filters->each(function ($filter) {
            if(in_array(get_class($filter), Filters::NO_FILTERS)) {
                throw new FilterException("You can't use ".get_class($filter)." inside filters.");
            }
        });

        return $filters;
    }
}
