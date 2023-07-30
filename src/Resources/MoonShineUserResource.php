<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use MoonShine\Actions\ExportAction;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Date;
use MoonShine\Fields\Email;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Password;
use MoonShine\Fields\PasswordRepeat;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Text;
use MoonShine\Filters\TextFilter;
use MoonShine\Http\Controllers\PermissionController;
use MoonShine\ItemActions\ItemAction;
use MoonShine\Models\MoonshineUser;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;

class MoonShineUserResource extends ModelResource
{
    public string $model = MoonshineUser::class;

    public string $titleField = 'name';

    public static bool $withPolicy = true;

    public function fields(): array
    {
        return [
            Block::make('', [
                Tabs::make([
                    Tab::make('Main', [
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
                            ->showOnExport()
                            ->disk('public')
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

                    Tab::make(trans('moonshine::ui.resource.password'), [
                        Heading::make('Change password'),

                        Password::make(trans('moonshine::ui.resource.password'), 'password')
                            ->customAttributes(['autocomplete' => 'new-password'])
                            ->hideOnIndex()
                            ->eye(),

                        PasswordRepeat::make(trans('moonshine::ui.resource.repeat_password'), 'password_repeat')
                            ->customAttributes(['autocomplete' => 'confirm-password'])
                            ->hideOnIndex()
                            ->eye(),
                    ]),
                ]),
            ]),
        ];
    }

    public function itemActions(): array
    {
        return [
            ItemAction::make('Login as', function (MoonshineUser $item) {
                auth(config('moonshine.auth.guard'))->login($item);
            }, 'Success')->icon('users'),
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
        //parent::resolveRoutes();

        Route::prefix('resource')->group(function () {
            Route::post(
                "{$this->uriKey()}/{" . $this->routeParam() . "}/permissions",
                PermissionController::class
            )
                ->name("{$this->routeNameAlias()}.permissions");
        });
    }

    public function pages(): array
    {
        return [
            IndexPage::make('Пользователи'),

            FormPage::make(
                request('item')
                    ? 'Редактировать'
                    : 'Добавить'
            ),
        ];
    }
}
