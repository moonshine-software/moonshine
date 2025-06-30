<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use Throwable;

trait ResourceWithFields
{
    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getIndexFields(): Fields
    {
        /** @var ?Fields $fields */
        $fields = $this->getIndexPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            return Fields::make($this->indexFields());
        }

        $fields->ensure([FieldContract::class, FieldsWrapperContract::class]);

        return $fields;
    }

    /**
     * @return list<FieldContract|ComponentContract>
     */
    protected function formFields(): iterable
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getFormFields(bool $withOutside = false): Fields
    {
        /** @var ?Fields $fields */
        $fields = $this->getFormPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            $fields = Fields::make($this->formFields());
        }

        return $fields->formFields(withOutside: $withOutside);
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function getDetailFields(bool $withOutside = false, bool $onlyOutside = false): Fields
    {
        /** @var ?Fields $fields */
        $fields = $this->getDetailPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            $fields = Fields::make($this->detailFields());
        }

        $fields->ensure([FieldsWrapperContract::class, FieldContract::class, ModelRelationField::class]);

        return $fields->detailFields(withOutside: $withOutside, onlyOutside: $onlyOutside);
    }

    /**
     * @return Fields<ModelRelationField>
     * @throws Throwable
     */
    public function getOutsideFields(): Fields
    {
        /**
         * @var ?Fields $fields
         */
        $fields = $this->getFormPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            $fields = Fields::make($this->formFields());
        }

        /** @var Fields<ModelRelationField> */
        return $fields
            ->onlyFields()
            ->onlyOutside();
    }
}
