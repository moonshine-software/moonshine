<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\Laravel\Buttons\CreateButton;
use MoonShine\Laravel\Buttons\DeleteButton;
use MoonShine\Laravel\Buttons\DetailButton;
use MoonShine\Laravel\Buttons\EditButton;
use MoonShine\Laravel\Buttons\MassDeleteButton;
use MoonShine\UI\Collections\ActionButtons;

trait ResourceWithButtons
{
    public function getIndexButtons(): ActionButtonsContract
    {
        return ActionButtons::make(
            $this->indexButtons() === [] ? $this->buttons() : $this->indexButtons()
        );
    }

    public function getFormButtons(): ActionButtonsContract
    {
        return $this->getWithoutBulkButtons($this->formButtons());
    }

    public function getDetailButtons(): ActionButtonsContract
    {
        return $this->getWithoutBulkButtons($this->detailButtons());
    }

    protected function getWithoutBulkButtons(array $customButtons = []): ActionButtonsContract
    {
        return ActionButtons::make(
            $customButtons === []
                ? $this->buttons()
                : $customButtons
        )->withoutBulk();
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function buttons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function indexButtons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function formButtons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function detailButtons(): array
    {
        return [];
    }

    public function getFormBuilderButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->formBuilderButtons());
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function formBuilderButtons(): array
    {
        return [];
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

    /**
     * @return list<ActionButtonContract>
     */
    public function getIndexItemButtons(): array
    {
        return [
            ...$this->getIndexButtons(),
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
        ];
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function getFormItemButtons(): array
    {
        return [
            ...$this->getFormButtons(),
            $this->getDetailButton(),
            $this->getDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: false
            ),
        ];
    }

    /**
     * @return list<ActionButtonContract>
     */
    public function getDetailItemButtons(): array
    {
        return [
            ...$this->getDetailButtons(),
            $this->getEditButton(
                isAsync: $this->isAsync(),
            ),
            $this->getDeleteButton(
                redirectAfterDelete: $this->getRedirectAfterDelete(),
                isAsync: false
            ),
        ];
    }
}
