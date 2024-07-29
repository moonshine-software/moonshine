<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use MoonShine\Laravel\ImportExport\Contracts\HasImportExportContract;
use MoonShine\Laravel\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

#[Icon('bookmark')]
class MoonShineUserRoleResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    public string $model = MoonshineUserRole::class;

    public string $column = 'name';

    protected bool $createInModal = true;

    protected bool $detailInModal = true;

    protected bool $editInModal = true;

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return __('moonshine::ui.resource.role');
    }

    public function indexButtons(): ListOf
    {
        return parent::indexButtons()->except(fn (ActionButton $btn): bool => $btn->getName() === 'detail-button');
    }

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('moonshine::ui.resource.role_name'), 'name'),
        ];
    }

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function formFields(): array
    {
        return [
            Box::make([
                ID::make()->sortable(),
                Text::make(__('moonshine::ui.resource.role_name'), 'name')
                    ->required(),
            ]),
        ];
    }

    public function exportFields(): array
    {
        return $this->indexFields();
    }

    public function importFields(): array
    {
        return $this->indexFields();
    }

    /**
     * @return array{name: string}
     */
    public function rules($item): array
    {
        return [
            'name' => ['required', 'min:5'],
        ];
    }

    public function search(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
