<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use MoonShine\Enums\PageType;

trait WithIsNowOnRoute
{
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
        return (
            is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') === PageType::FORM->value
        ) || (
            is_null(request('resourceItem'))
            && request('pageUri') === PageType::FORM->value
        );
    }

    public function isNowOnUpdateForm(): bool
    {
        return (
            ! is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') === PageType::FORM->value
        ) || (
            ! is_null(request('resourceItem'))
            && request('pageUri') === PageType::FORM->value
        );
    }
}
