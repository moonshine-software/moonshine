<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Laravel\Buttons\CreateButton;
use MoonShine\Laravel\Buttons\DeleteButton;
use MoonShine\Laravel\Buttons\DetailButton;
use MoonShine\Laravel\Buttons\EditButton;
use MoonShine\Laravel\Buttons\FiltersButton;
use MoonShine\Laravel\Buttons\MassDeleteButton;
use MoonShine\Support\ListOf;
use MoonShine\UI\Collections\ActionButtons;

trait ResourceWithButtons
{
    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function topButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->getCreateButton(isAsync: $this->isAsync()),
        ]);
    }

    /**
     * TableBuilder row buttons
     *
     * @return ListOf<ActionButtonContract>
     */
    protected function indexButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->getDetailButton(),
            $this->getEditButton(
                isAsync: $this->isAsync()
            ),
            $this->getDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: $this->isAsync()
            ),
            $this->getMassDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: $this->isAsync()
            ),
        ]);
    }

    /**
     * Top form buttons
     *
     * @return ListOf<ActionButtonContract>
     */
    protected function formButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->getDetailButton(),
            $this->getDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: false
            ),
        ]);
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function detailButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, [
            $this->getEditButton(
                isAsync: $this->isAsync(),
            ),
            $this->getDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: false
            ),
        ]);
    }

    /**
     * Form buttons after submit
     *
     * @return ListOf<ActionButtonContract>
     */
    protected function formBuilderButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    public function getTopButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->topButtons()->toArray());
    }

    public function getIndexButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->indexButtons()->toArray(),
        );
    }

    public function getFormButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->formButtons()->toArray()
        )->withoutBulk();
    }

    public function getDetailButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->detailButtons()->toArray()
        )->withoutBulk();
    }

    public function getFormBuilderButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->formBuilderButtons()->toArray()
        )->withoutBulk();
    }

    public function getCreateButton(
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-create-modal'
    ): ActionButtonContract {
        return $this->modifyCreateButton(
            CreateButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync,
                modalName: $modalName
            )
        );
    }

    public function getEditButton(
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'resource-edit-modal'
    ): ActionButtonContract {
        return $this->modifyEditButton(
            EditButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync,
                modalName: $modalName,
            )
        );
    }

    public function getDetailButton(
        string $modalName = 'resource-detail-modal',
        bool $isSeparateModal = true
    ): ActionButtonContract {
        return $this->modifyDetailButton(
            DetailButton::for(
                $this,
                $modalName,
                $isSeparateModal
            )
        );
    }

    public function getDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
        string $modalName = 'resource-delete-modal',
    ): ActionButtonContract {
        return $this->modifyDeleteButton(
            DeleteButton::for(
                $this,
                componentName: $componentName,
                redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
                isAsync: $isAsync,
                modalName: $modalName
            )
        );
    }

    public function getFiltersButton(): ActionButtonContract
    {
        return $this->modifyFiltersButton(FiltersButton::for($this));
    }

    public function getMassDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
        string $modalName = 'resource-mass-delete-modal',
    ): ActionButtonContract {
        return $this->modifyMassDeleteButton(
            MassDeleteButton::for(
                $this,
                componentName: $componentName,
                redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
                isAsync: $isAsync,
                modalName: $modalName
            )
        );
    }

    protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyFiltersButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    protected function modifyMassDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }
}
