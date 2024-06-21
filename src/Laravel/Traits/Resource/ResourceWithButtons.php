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
    /**
     * @return list<ActionButton>
     */
    public function getIndexButtons(): array
    {
        return empty($this->indexButtons()) ? $this->buttons() : $this->indexButtons();
    }

    /**
     * @return list<ActionButton>
     */
    public function getFormButtons(): array
    {
        return $this->getWithoutBulkButtons($this->formButtons());
    }

    /**
     * @return list<ActionButton>
     */
    public function getDetailButtons(): array
    {
        return $this->getWithoutBulkButtons($this->detailButtons());
    }

    /**
     * @return list<ActionButton>
     */
    protected function getWithoutBulkButtons(array $buttonsType = []): array
    {
        return ActionButtons::make(
            $buttonsType === []
                ? $this->buttons()
                : $buttonsType
        )
            ->withoutBulk()
            ->toArray();
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

    /**
     * @return list<ActionButton>
     */
    public function getFormBuilderButtons(): array
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

    public function getDetailButton(bool $isAsync = true): ActionButton
    {
        return $this->modifyDetailButton(
            DetailButton::for(
                $this,
                isAsync: $isAsync
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
            $this->getDetailButton(
                isAsync: $this->isAsync()
            ),
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
