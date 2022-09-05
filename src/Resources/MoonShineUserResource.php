<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

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
use Leeto\MoonShine\Filters\TextFilter;
use Leeto\MoonShine\Models\MoonshineUser;
use Leeto\MoonShine\RowActions\EditRowAction;

final class MoonShineUserResource extends ModelResource
{
    public static string $model = MoonshineUser::class;

    public string $column = 'name';

    public function title(): string
    {
        return trans('moonshine::ui.base_resource.admins_title');
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
                    trans('moonshine::ui.base_resource.role'),
                    'moonshine_user_role_id',
                    new MoonShineUserRoleResource()
                )
                    ->showOnExport(),

                Text::make(trans('moonshine::ui.base_resource.name'), 'name')
                    ->required()
                    ->showOnExport(),

                Avatar::make(trans('moonshine::ui.base_resource.avatar'), 'avatar')
                    ->removable()
                    ->showOnExport()
                    ->disk(config('filesystems.default'))
                    ->dir('moonshine_users')
                    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                Date::make(trans('moonshine::ui.base_resource.created_at'), 'created_at')
                    ->format("d.m.Y")
                    ->default(now()->toDateTimeString())
                    ->sortable()
                    ->hideOnForm()
                    ->showOnExport(),

                Email::make(trans('moonshine::ui.base_resource.email'), 'email')
                    ->sortable()
                    ->showOnExport()
                    ->required(),

                Heading::make('Доступы'),

                Password::make(trans('moonshine::ui.base_resource.password'), 'password')
                    ->customAttributes(['autocomplete' => 'new-password'])
                    ->hideOnIndex(),

                PasswordRepeat::make(trans('moonshine::ui.base_resource.repeat_password'), 'password_repeat')
                    ->customAttributes(['autocomplete' => 'confirm-password'])
                    ->hideOnIndex(),
            ])
        ];
    }

    public function rowActions(Model $item): array
    {
        return array_filter([
            $this->can('edit', $item)
                ? EditRowAction::make('Edit')
                ->icon('pencil')
                ->customAttributes(['style' => 'background: gray'])
                : false
        ]);
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
            TextFilter::make(trans('moonshine::ui.base_resource.name'), 'name'),
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make(trans('moonshine::ui.export')),
        ];
    }
}
