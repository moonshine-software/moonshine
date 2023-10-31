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
        return request()?->route('pageUri') === PageType::INDEX->value
            || request('pageUri') === PageType::INDEX->value;
    }

    public function isNowOnDetail(): bool
    {
        return request()?->route('pageUri') === PageType::DETAIL->value
            || request('pageUri') === PageType::DETAIL->value;
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
            && request()?->route('pageUri') === PageType::FORM->value
        ) && (
            is_null(request('resourceItem'))
            && request('pageUri') === PageType::FORM->value
        );
    }

    public function isNowOnUpdateForm(): bool
    {
        if ($this->forceNowOnUpdate) {
            return true;
        }

        return (
            ! is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') === PageType::FORM->value
        ) || (
            ! is_null(request('resourceItem'))
            && request('pageUri') === PageType::FORM->value
        );
    }
}
