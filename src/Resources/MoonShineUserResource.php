<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use MoonShine\Actions\ExportAction;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Grid;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Text;
use MoonShine\Filters\TextFilter;
use MoonShine\FormComponents\PermissionFormComponent;
use MoonShine\Http\Controllers\PermissionController;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;

class MoonShineUserResource extends Resource
{
    public static string $model = MoonshineUser::class;

    public string $titleField = 'name';

    protected static bool $system = true;

    public function title(): string
    {
        return trans('moonshine::ui.resource.admins_title');
    }

    public function fields(): array
    {
        return [
            Grid::make([
               Column::make([
                   Block::make(trans('moonshine::ui.resource.main_information'), [
                       ID::make()
                           ->sortable()
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

                       Image::make(trans('moonshine::ui.resource.avatar'), 'avatar')
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
                   ]),

                   Block::make(trans('moonshine::ui.resource.change_password'), [
                       Password::make(trans('moonshine::ui.resource.password'), 'password')
                           ->customAttributes(['autocomplete' => 'new-password'])
                           ->hideOnIndex()
                           ->hideOnExport()
                           ->hideOnDetail(),

                       PasswordRepeat::make(trans('moonshine::ui.resource.repeat_password'), 'password_repeat')
                           ->customAttributes(['autocomplete' => 'confirm-password'])
                           ->hideOnIndex()
                           ->hideOnExport()
                           ->hideOnDetail(),
                   ]),
               ]),
            ]),
        ];
    }

    public function components(): array
    {
        return [
            PermissionFormComponent::make('Permissions')
                ->canSee(fn ($user) => $user->moonshine_user_role_id === MoonshineUserRole::DEFAULT_ROLE_ID),
        ];
    }

    public function rules($item): array
    {
        return [
            'name' => 'required',
            'moonshine_user_role_id' => 'required',
            'email' => [
                'sometimes',
                'bail',
                'required',
                'email',
                Rule::unique('moonshine_users')->ignoreModel($item),
            ],
            'password' => ! $item->exists
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
            TextFilter::make(trans('moonshine::ui.resource.name'), 'name'),
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make(trans('moonshine::ui.export')),
        ];
    }

    public function resolveRoutes(): void
    {
        parent::resolveRoutes();

        Route::prefix('resource')->group(function () {
            Route::post(
                "{$this->uriKey()}/{".$this->routeParam()."}/permissions",
                PermissionController::class
            )->name("{$this->routeNameAlias()}.permissions");
        });
    }
}
