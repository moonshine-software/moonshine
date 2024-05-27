<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Support\Attributes\Icon;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Tabs\Tabs;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Text;

#[Icon('users')]
class MoonShineUserResource extends ModelResource
{
    public string $model = MoonshineUser::class;

    public string $column = 'name';

    public array $with = ['moonshineUserRole'];

    public function title(): string
    {
        return __('moonshine::ui.resource.admins_title');
    }

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(
                __('moonshine::ui.resource.role'),
                'moonshineUserRole',
                formatted: static fn (MoonshineUserRole $model) => $model->name,
                resource: MoonShineUserRoleResource::class,
            )->badge('purple'),

            Text::make(__('moonshine::ui.resource.name'), 'name'),

            Image::make(__('moonshine::ui.resource.avatar'), 'avatar'),

            Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                ->format("d.m.Y")
                ->sortable(),

            Email::make(__('moonshine::ui.resource.email'), 'email')
                ->sortable(),
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
                Tabs::make([
                    Tab::make(__('moonshine::ui.resource.main_information'), [
                        ID::make()->sortable(),

                        BelongsTo::make(
                            __('moonshine::ui.resource.role'),
                            'moonshineUserRole',
                            formatted: static fn (MoonshineUserRole $model) => $model->name,
                            resource: MoonShineUserRoleResource::class,
                        )
                            ->valuesQuery(fn (Builder $q) => $q->select(['id', 'name'])),

                        Text::make(__('moonshine::ui.resource.name'), 'name')
                            ->required(),

                        Image::make(__('moonshine::ui.resource.avatar'), 'avatar')
                            ->disk(moonshineConfig()->getDisk())
                            ->dir('moonshine_users')
                            ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

                        Date::make(__('moonshine::ui.resource.created_at'), 'created_at')
                            ->format("d.m.Y")
                            ->default(now()->toDateTimeString()),

                        Email::make(__('moonshine::ui.resource.email'), 'email')
                            ->required(),
                    ]),

                    Tab::make(__('moonshine::ui.resource.password'), [
                        Heading::make('Change password'),

                        Password::make(__('moonshine::ui.resource.password'), 'password')
                            ->customAttributes(['autocomplete' => 'new-password'])
                            ->eye(),

                        PasswordRepeat::make(__('moonshine::ui.resource.repeat_password'), 'password_repeat')
                            ->customAttributes(['autocomplete' => 'confirm-password'])
                            ->eye(),
                    ]),
                ]),
            ]),
        ];
    }

    /**
     * @return array{name: string, moonshine_user_role_id: string, email: array, password: string}
     */
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
            'password' => $item->exists
                ? 'sometimes|nullable|min:6|required_with:password_repeat|same:password_repeat'
                : 'required|min:6|required_with:password_repeat|same:password_repeat',
        ];
    }

    public function search(): array
    {
        return [
            'id',
            'name',
        ];
    }

    public function filters(): array
    {
        return [
            Email::make('E-mail', 'email')
        ];
    }
}
