<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Enums\PageType;

trait WithIsNowOnRoute
{
    protected bool $forceNowOnIndex = false;

    protected bool $forceNowOnDetail = false;

    protected bool $forceNowOnCreate = false;

    protected bool $forceNowOnUpdate = false;

    public function forceNowOnIndex(): static
    {
        $this->forceNowOnIndex = true;

        return $this;
    }

    public function forceNowOnDetail(): static
    {
        $this->forceNowOnDetail = true;

        return $this;
    }

    public function forceNowOnCreate(): static
    {
        $this->forceNowOnCreate = true;

        return $this;
    }

    public function forceNowOnUpdate(): static
    {
        $this->forceNowOnUpdate = true;

        return $this;
    }

    public function isNowOnIndex(): bool
    {
        if ($this->forceNowOnDetail || $this->forceNowOnCreate || $this->forceNowOnUpdate) {
            return false;
        }

        if ($this->forceNowOnIndex) {
            return true;
        }

        return moonshineRequest()->findPage()?->pageType() === PageType::INDEX;
    }

    public function isNowOnDetail(): bool
    {
        if ($this->forceNowOnIndex || $this->forceNowOnCreate || $this->forceNowOnUpdate) {
            return false;
        }

        if ($this->forceNowOnDetail) {
            return true;
        }

        return moonshineRequest()->findPage()?->pageType() === PageType::DETAIL;
    }

    public function isNowOnForm(): bool
    {
        if ($this->forceNowOnDetail || $this->forceNowOnIndex) {
            return false;
        }

        return $this->isNowOnCreateForm()
            || $this->isNowOnUpdateForm();
    }

    public function isNowOnCreateForm(): bool
    {
        if ($this->forceNowOnDetail || $this->forceNowOnIndex) {
            return false;
        }

        if ($this->forceNowOnCreate) {
            return true;
        }

        if (moonshineRequest()->routeIs('moonshine.crud.store')) {
            return true;
        }

        if (moonshineRequest()->routeIs('moonshine.crud.*')) {
            return false;
        }

        return is_null(moonshineRequest()->getItemID())
            && moonshineRequest()->findPage()?->pageType() === PageType::FORM;
    }

    public function isNowOnUpdateForm(): bool
    {
        if ($this->forceNowOnDetail || $this->forceNowOnIndex) {
            return false;
        }

        if ($this->forceNowOnUpdate) {
            return true;
        }

        if (moonshineRequest()->routeIs('moonshine.crud.update')) {
            return true;
        }

        if (moonshineRequest()->routeIs('moonshine.crud.*')) {
            return false;
        }

        return moonshineRequest()->getItemID()
            && moonshineRequest()->findPage()?->pageType() === PageType::FORM;
    }
}
