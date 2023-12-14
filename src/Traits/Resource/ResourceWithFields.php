<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\Enums\PageType;
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

    public function formFields(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getFormFields(): Fields
    {
        $fields = $this->getPages()
            ->findByType(PageType::FORM)
            ?->fields();

        if (empty($fields)) {
            $fields = $this->formFields()
                ?: $this->fields();
        }

        return Fields::make($fields)
            ->formFields()
            ->withoutOutside();
    }

    /**
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

    public function detailFields(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getDetailFields(): Fields
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
            ->detailFields();
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

        $filters->each(function ($filter): void {
            if (in_array($filter::class, Filters::NO_FILTERS)) {
                throw new FilterException("You can't use " . $filter::class . " inside filters.");
            }
        });

        return $filters;
    }
}
