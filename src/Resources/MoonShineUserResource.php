<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Resources;

use Leeto\MoonShine\Actions\ExportAction;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Email;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\PasswordRepeat;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\Image;
use Leeto\MoonShine\Fields\Date;
use Leeto\MoonShine\Fields\Password;
use Leeto\MoonShine\Filters\TextFilter;

use Leeto\MoonShine\Models\MoonshineUser;

class MoonShineUserResource extends Resource
{
	public static string $model = MoonshineUser::class;

    public static string $title = 'Администраторы';

    public string $titleField = 'name';

    protected static bool $system = true;

    public function fields(): array
    {
        return [
            ID::make()
                ->sortable()
                ->showOnExport(),

            BelongsTo::make('Роль', 'moonshine_user_role_id', new MoonShineUserRoleResource())
                ->showOnExport(),

            Text::make('Имя', 'name')
                ->required()
                ->showOnExport(),

            Image::make('Аватар', 'avatar')
                ->removable()
                ->showOnExport()
                ->disk(config('filesystems.default'))
                ->dir('moonshine_users')
                ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif']),

            Date::make('Дата создания', 'created_at')
                ->format("d.m.Y")
                ->default(now())
                ->sortable()
                ->hideOnForm()
                ->showOnExport(),

            Email::make('E-mail', 'email')
                ->sortable()
                ->showOnExport()
                ->required(),

            Password::make('Пароль', 'password')->hideOnIndex(),
            PasswordRepeat::make('Повторите пароль', 'password_repeat')->hideOnIndex(),
        ];
    }

    public function rules($item): array
    {
        return [
            'name' => 'required',
            'moonshine_user_role_id' => 'required',
            'email' => 'sometimes|bail|required|email|unique:moonshine_users,email' . ($item->exists ? ",$item->id" : ''),
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
            TextFilter::make('Имя', 'name'),
        ];
    }

    public function actions(): array
    {
        return [
            ExportAction::make('Экспорт'),
        ];
    }
}
