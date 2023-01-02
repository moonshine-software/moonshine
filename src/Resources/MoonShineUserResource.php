<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Decorations\Heading;
use Leeto\MoonShine\Decorations\Tab;
use Leeto\MoonShine\Fields\Avatar;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Date;
use Leeto\MoonShine\Fields\Email;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Password;
use Leeto\MoonShine\Fields\PasswordRepeat;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Filters\ModelFilter;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\RowActions\DeleteRowAction;
use Leeto\MoonShine\RowActions\EditRowAction;
use Leeto\MoonShine\RowActions\ShowRowAction;

final class MoonShineUserResource extends ModelResource
{
    public static string $model = MoonshineUser::class;

    public string $column = 'name';

    public function title(): string
    {
        return trans('moonshine::ui.resource.admins_title');
    }

    public function fields(): array
    {
        return [
            Heading::make('Доступы'),

            Tab::make('Основное', [
                ID::make()
                    ->sortable()
                    ->hideOnForm()
                    ->showOnExport(),

                BelongsTo::make(
                    trans('moonshine::ui.resource.role'),
                    'moonshine_user_role_id',
                    new MoonShineUserRoleResource()
                )
                    ->showOnExport(),

                Text::make(trans('moonshine::ui.resource.name'), 'name')
                    ->required()
                    ->showOnExport(),

                Avatar::make(trans('moonshine::ui.resource.avatar'), 'avatar')
                    ->removable()
                    ->showOnExport()
                    ->disk(config('filesystems.default'))
                    ->dir('moonshine_users')
                    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                Date::make(trans('moonshine::ui.resource.created_at'), 'created_at')
                    ->format("d.m.Y")
                    ->default(now()->toDateTimeString())
                    ->sortable()
                    ->hideOnForm()
                    ->showOnExport(),

                Email::make(trans('moonshine::ui.resource.email'), 'email')
                    ->sortable()
                    ->showOnExport()
                    ->required(),

                Heading::make('Доступы'),

                Password::make(trans('moonshine::ui.resource.password'), 'password')
                    ->customAttributes(['autocomplete' => 'new-password'])
                    ->hideOnIndex(),

                PasswordRepeat::make(trans('moonshine::ui.resource.repeat_password'), 'password_repeat')
                    ->customAttributes(['autocomplete' => 'confirm-password'])
                    ->hideOnIndex(),
            ])
        ];
    }

    public function rowActions(Model $item): array
    {
        return [
            ShowRowAction::make(__('moonshine::ui.show')),
            EditRowAction::make(__('moonshine::ui.edit')),
            DeleteRowAction::make(__('moonshine::ui.delete'))
        ];
    }

    public function rules($item): array
    {
        return [
            'name' => 'required',
            'moonshine_user_role_id' => 'required',
            'email' => 'sometimes|bail|required|email|unique:moonshine_users,email'.($item->exists ? ",$item->id" : ''),
            'password' => !$item->exists
                ? 'required|min:6|required_with:password_repeat|same:password_repeat'
                : 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    public function search(): array
    {
        return ['id', 'name'];
    }

    public function filters(): array
    {
        return [
            ModelFilter::make(
                trans('moonshine::ui.resource.name'),
                [
                    Text::make(trans('moonshine::ui.resource.name'), 'name')
                ],
                function (Builder $query, $value) {
                    return $query->where('name', $value['name']);
                }
            )
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make(trans('moonshine::ui.export')),
        ];
    }
}
