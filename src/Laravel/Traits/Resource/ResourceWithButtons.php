<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
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

    protected function customIndexButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function getCustomIndexButtons(): array
    {
        return $this->customIndexButtons()->toArray();
    }

    public function getIndexButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->getCustomIndexButtons(),
            ...$this->indexButtons()->toArray(),
        ]);
    }

    protected function customFormButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function getCustomFormButtons(): array
    {
        return $this->customFormButtons()->toArray();
    }

    public function getFormButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->getCustomFormButtons(),
            ...$this->formButtons()->toArray(),
        ])->withoutBulk();
    }

    protected function customDetailButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function getCustomDetailButtons(): array
    {
        return $this->customIndexButtons()->toArray();
    }

    public function getDetailButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->getCustomDetailButtons(),
            ...$this->detailButtons()->toArray(),
        ])->withoutBulk();
    }

    public function getFormBuilderButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->formBuilderButtons()->toArray()
        )->withoutBulk();
    }

    protected function modifyCreateButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getCreateButton(
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'create-modal'
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

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getEditButton(
        ?string $componentName = null,
        bool $isAsync = true,
        string $modalName = 'edit-modal'
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

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getDetailButton(string $modalName = 'detail-modal'): ActionButtonContract
    {
        return $this->modifyDetailButton(
            DetailButton::for(
                $this,
                $modalName,
            )
        );
    }

    protected function modifyDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
        string $modalName = 'delete-modal',
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

    protected function modifyFiltersButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getFiltersButton(): ActionButtonContract
    {
        return $this->modifyFiltersButton(FiltersButton::for($this));
    }

    protected function modifyMassDeleteButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getMassDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
        string $modalName = 'mass-delete-modal',
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
}
