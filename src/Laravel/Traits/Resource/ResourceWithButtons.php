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
     * If the index, detail and form buttons are empty, then take these or merge these
     *
     * @return ListOf<ActionButtonContract>
     */
    public function buttons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    public function topButtons(): ListOf
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
    public function indexButtons(): ListOf
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
    public function formButtons(): ListOf
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
    public function detailButtons(): ListOf
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
    public function formBuilderButtons(): ListOf
    {
        return new ListOf(ActionButtonContract::class, []);
    }

    public function getTopButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->topButtons()->toArray());
    }

    public function getIndexButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->buttons()->toArray(),
            ...$this->indexButtons()->toArray(),
        ]);
    }

    public function getFormButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->buttons()->toArray(),
            ...$this->formButtons()->toArray(),
        ])->withoutBulk();
    }

    public function getDetailButtons(): ActionButtonsContract
    {
        return ActionButtons::make([
            ...$this->buttons()->toArray(),
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

    public function getCreateButton(?string $componentName = null, bool $isAsync = true): ActionButtonContract
    {
        return $this->modifyCreateButton(
            CreateButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync
            )
        );
    }

    protected function modifyEditButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getEditButton(?string $componentName = null, bool $isAsync = true): ActionButtonContract
    {
        return $this->modifyEditButton(
            EditButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync
            )
        );
    }

    protected function modifyDetailButton(ActionButtonContract $button): ActionButtonContract
    {
        return $button;
    }

    public function getDetailButton(): ActionButtonContract
    {
        return $this->modifyDetailButton(
            DetailButton::for(
                $this
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
        bool $isAsync = true
    ): ActionButtonContract {
        return $this->modifyDeleteButton(
            DeleteButton::for(
                $this,
                componentName: $componentName,
                redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
                isAsync: $isAsync
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
        bool $isAsync = true
    ): ActionButtonContract {
        return $this->modifyMassDeleteButton(
            MassDeleteButton::for(
                $this,
                componentName: $componentName,
                redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
                isAsync: $isAsync
            )
        );
    }
}
