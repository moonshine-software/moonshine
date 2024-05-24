<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use MoonShine\Buttons\CreateButton;
use MoonShine\Buttons\DeleteButton;
use MoonShine\Buttons\DetailButton;
use MoonShine\Buttons\EditButton;
use MoonShine\Buttons\MassDeleteButton;
use MoonShine\Components\ActionButtons\ActionButton;
use MoonShine\Components\ActionButtons\ActionButtons;

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
        return $this->withoutBulk($this->formButtons());
    }

    /**
     * @return list<ActionButton>
     */
    public function getDetailButtons(): array
    {
        return $this->withoutBulk($this->detailButtons());
    }

    /**
     * @return list<ActionButton>
     */
    protected function withoutBulk(array $buttonsType = []): array
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

    public function getCreateButton(?string $componentName = null, bool $isAsync = true): ActionButton
    {
        return CreateButton::for(
            $this,
            componentName: $componentName,
            isAsync: $isAsync
        );
    }

    public function getEditButton(?string $componentName = null, bool $isAsync = true): ActionButton
    {
        return EditButton::for(
            $this,
            componentName: $componentName,
            isAsync: $isAsync
        );
    }

    public function getDetailButton(bool $isAsync = true): ActionButton
    {
        return DetailButton::for(
            $this,
            isAsync: $isAsync
        );
    }

    public function getDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true
    ): ActionButton {
        return DeleteButton::for(
            $this,
            componentName: $componentName,
            redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
            isAsync: $isAsync
        );
    }

    public function getMassDeleteButton(
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true
    ): ActionButton {
        return MassDeleteButton::for(
            $this,
            componentName: $componentName,
            redirectAfterDelete: $isAsync ? '' : $redirectAfterDelete,
            isAsync: $isAsync
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
                redirectAfterDelete: $this->redirectAfterDelete(),
                isAsync: $this->isAsync()
            ),
            $this->getMassDeleteButton(
                redirectAfterDelete: $this->redirectAfterDelete(),
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
                redirectAfterDelete: $this->redirectAfterDelete(),
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
                redirectAfterDelete: $this->redirectAfterDelete(),
                isAsync: false
            ),
        ];
    }
}
