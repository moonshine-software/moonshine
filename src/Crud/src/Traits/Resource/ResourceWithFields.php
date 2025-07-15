<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits\Resource;

use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\UI\Contracts\FieldsWrapperContract;
use Throwable;

/**
 * @template TFields of Fields = Fields
 */
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
     * @return TFields
     */
    public function getIndexFields(): FieldsContract
    {
        /** @var null|TFields $fields */
        $fields = $this->getIndexPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            /** @var TFields */
            return $this->getCore()->getFieldsCollection($this->indexFields());
        }

        $fields->ensure([FieldContract::class, FieldsWrapperContract::class]);

        /** @var TFields */
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
     * @return TFields
     */
    public function getFormFields(bool $withOutside = false): FieldsContract
    {
        /** @var null|TFields $fields */
        $fields = $this->getFormPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            /** @var TFields */
            $fields = $this->getCore()->getFieldsCollection($this->formFields());
        }

        /** @var TFields */
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
     * @return TFields
     */
    public function getDetailFields(bool $withOutside = false, bool $onlyOutside = false): FieldsContract
    {
        /** @var null|TFields $fields */
        $fields = $this->getDetailPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            /** @var TFields */
            $fields = $this->getCore()->getFieldsCollection($this->detailFields());
        }

        $fields->ensure([FieldsWrapperContract::class, FieldContract::class]);

        /** @var TFields */
        return $fields->detailFields(withOutside: $withOutside, onlyOutside: $onlyOutside);
    }

    /**
     * @return TFields
     * @throws Throwable
     */
    public function getOutsideFields(): FieldsContract
    {
        /**
         * @var null|TFields $fields
         */
        $fields = $this->getFormPage()?->getFields();

        if ($fields === null || $fields->isEmpty()) {
            /** @var TFields */
            $fields = $this->getCore()->getFieldsCollection($this->formFields());
        }

        /** @var TFields */
        return $fields
            ->onlyFields()
            ->onlyOutside();
    }
}
