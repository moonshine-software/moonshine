<?php

declare(strict_types=1);

namespace MoonShine\Crud\Traits\Resource;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Crud\Buttons\CreateButton;
use MoonShine\Crud\Buttons\DeleteButton;
use MoonShine\Crud\Buttons\DetailButton;
use MoonShine\Crud\Buttons\EditButton;
use MoonShine\Crud\Buttons\FiltersButton;
use MoonShine\Crud\Buttons\MassDeleteButton;
use MoonShine\Crud\Forms\FiltersForm;
use MoonShine\Crud\Resources\CrudResource;
use Psr\SimpleCache\InvalidArgumentException;
use Throwable;

trait ResourceWithButtons
{
    /**
     * @throws Throwable
     */
    public function getCreateButton(
        ?CrudResource $resource = null,
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-create-modal',
    ): ActionButtonContract {
        /** @var string $label */
        $label = $this->getCore()->getTranslator()->get('moonshine::ui.create');

        return CreateButton::for(
            $label,
            $resource ?? $this,
            componentName: $componentName,
            isAsync: $isAsync,
            modalName: $modalName,
        );
    }

    /**
     * @throws Throwable
     */
    public function getEditButton(
        ?CrudResource $resource = null,
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-edit-modal',
    ): ActionButtonContract {
        return EditButton::for(
            $resource ?? $this,
            componentName: $componentName,
            isAsync: $isAsync,
            modalName: $modalName,
            query: $isAsync ? $this->getButtonDefaultQuery() : [],
        );
    }

    public function getDetailButton(
        ?CrudResource $resource = null,
        string $modalName = 'resource-detail-modal',
        bool $isSeparateModal = true,
    ): ActionButtonContract {
        return DetailButton::for(
            $this->getCore()->getTranslator()->get('moonshine::ui.show'),
            $resource ?? $this,
            $modalName,
            $isSeparateModal,
        );
    }

    public function getDeleteButton(
        ?CrudResource $resource = null,
        ?string $componentName = null,
        ?string $redirectAfterDelete = null,
        bool $isAsync = true,
        string $modalName = 'resource-delete-modal',
    ): ActionButtonContract {
        return DeleteButton::for(
            $resource ?? $this,
            componentName: $componentName,
            redirectAfterDelete: $isAsync ? null : $redirectAfterDelete,
            isAsync: $isAsync,
            modalName: $modalName,
            query: $isAsync ? $this->getButtonDefaultQuery() : [],
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getFiltersButton(?CrudResource $resource = null): ActionButtonContract
    {
        $form = $this->getCore()->getConfig()->getForm('filters', FiltersForm::class, resource: $resource ?? $this, core: $this->getCore());

        return FiltersButton::for(
            label: $this->getCore()->getTranslator()->get('moonshine::ui.filters'),
            form: $form,
            resource: $resource ?? $this,
        );
    }

    public function getMassDeleteButton(
        ?CrudResource $resource = null,
        ?string $componentName = null,
        ?string $redirectAfterDelete = null,
        bool $isAsync = true,
        string $modalName = 'resource-mass-delete-modal',
    ): ActionButtonContract {
        return MassDeleteButton::for(
            $resource ?? $this,
            componentName: $componentName,
            redirectAfterDelete: $isAsync ? null : $redirectAfterDelete,
            isAsync: $isAsync,
            modalName: $modalName,
            query: $isAsync ? $this->getButtonDefaultQuery() : [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getButtonDefaultQuery(): array
    {
        return array_filter([
            $this->getQueryParamName('page') => $this->getCore()->getRequest()->getScalar($this->getQueryParamName('page')),
            $this->getQueryParamName('sort') => $this->getCore()->getRequest()->getScalar($this->getQueryParamName('sort')),
        ]);
    }
}
