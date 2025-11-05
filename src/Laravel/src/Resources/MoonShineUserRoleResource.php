<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\Pages\MoonShineUserRole\MoonShineUserRoleFormPage;
use MoonShine\Laravel\Pages\MoonShineUserRole\MoonShineUserRoleIndexPage;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

/**
 * @extends ModelResource<MoonshineUserRole, MoonShineUserRoleIndexPage, MoonShineUserRoleFormPage, null>
 */
#[Icon('bookmark')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(1)]
class MoonShineUserRoleResource extends ModelResource
{
    protected string $model = MoonshineUserRole::class;

    protected string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $cursorPaginate = true;

    public function getTitle(): string
    {
        return __('moonshine::ui.resource.role');
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            MoonShineUserRoleIndexPage::class,
            MoonShineUserRoleFormPage::class,
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
