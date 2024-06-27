<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use MoonShine\Laravel\Buttons\CreateButton;
use MoonShine\Laravel\Buttons\DeleteButton;
use MoonShine\Laravel\Buttons\DetailButton;
use MoonShine\Laravel\Buttons\EditButton;
use MoonShine\Laravel\Buttons\MassDeleteButton;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionButton;

trait ResourceWithButtons
{
    public function getIndexButtons(): ActionButtons
    {
        return ActionButtons::make(
            $this->indexButtons() === [] ? $this->buttons() : $this->indexButtons()
        );
    }

    public function getFormButtons(): ActionButtons
    {
        return $this->getWithoutBulkButtons($this->formButtons());
    }

    public function getDetailButtons(): ActionButtons
    {
        return $this->getWithoutBulkButtons($this->detailButtons());
    }

    protected function getWithoutBulkButtons(array $customButtons = []): ActionButtons
    {
        return ActionButtons::make(
            $customButtons === []
                ? $this->buttons()
                : $customButtons
        )->withoutBulk();
    }

    /**
     * @return list<ActionButton>
     */
    public function buttons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButton>
     */
    public function indexButtons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButton>
     */
    public function formButtons(): array
    {
        return [];
    }

    /**
     * @return list<ActionButton>
     */
    public function detailButtons(): array
    {
        return [];
    }

    public function getFormBuilderButtons(): ActionButtons
    {
        return ActionButtons::make($this->formBuilderButtons());
    }

    /**
     * @return list<ActionButton>
     */
    public function formBuilderButtons(): array
    {
        return [];
    }

    protected function modifyCreateButton(ActionButton $button): ActionButton
    {
        return $button;
    }

    public function getCreateButton(?string $componentName = null, bool $isAsync = true): ActionButton
    {
        return $this->modifyCreateButton(
            CreateButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync
            )
        );
    }

    protected function modifyEditButton(ActionButton $button): ActionButton
    {
        return $button;
    }

    public function getEditButton(?string $componentName = null, bool $isAsync = true): ActionButton
    {
        return $this->modifyEditButton(
            EditButton::for(
                $this,
                componentName: $componentName,
                isAsync: $isAsync
            )
        );
    }

    protected function modifyDetailButton(ActionButton $button): ActionButton
    {
        return $button;
    }

    public function getDetailButton(): ActionButton
    {
        return $this->modifyDetailButton(
            DetailButton::for(
                $this
            )
        );
    }

    protected function modifyDeleteButton(ActionButton $button): ActionButton
    {
        return $button;
    }

    public function getDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true
    ): ActionButton {
        return $this->modifyDeleteButton(
            DeleteButton::for(
                $this,
                componentName: $componentName,
                redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
                isAsync: $isAsync
            )
        );
    }

    protected function modifyMassDeleteButton(ActionButton $button): ActionButton
    {
        return $button;
    }

    public function getMassDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true
    ): ActionButton {
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
     * @return list<ActionButton>
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
     * @return list<ActionButton>
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
     * @return list<ActionButton>
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
