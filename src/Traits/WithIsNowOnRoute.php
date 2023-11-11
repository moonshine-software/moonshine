<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Enums\PageType;

trait WithIsNowOnRoute
{
    protected bool $forceNowOnCreate = false;

    protected bool $forceNowOnUpdate = false;

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
        return (request()?->route('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::INDEX)
            || (request('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::INDEX);
    }

    public function isNowOnDetail(): bool
    {
        return (request()?->route('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::DETAIL)
            || (request('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::DETAIL);
    }

    public function isNowOnForm(): bool
    {
        return $this->isNowOnCreateForm()
            || $this->isNowOnUpdateForm();
    }

    public function isNowOnCreateForm(): bool
    {
        if ($this->forceNowOnCreate) {
            return true;
        }

        return (
            is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::FORM
        ) && (
            is_null(request('resourceItem'))
            && request('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::FORM
        );
    }

    public function isNowOnUpdateForm(): bool
    {
        if ($this->forceNowOnUpdate) {
            return true;
        }

        return (
            ! is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::FORM
        ) || (
            ! is_null(request('resourceItem'))
            && request('pageUri') && moonshineRequest()->getPage()->pageType() === PageType::FORM
        );
    }
}
