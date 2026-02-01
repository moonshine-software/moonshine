<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Modal;
use Throwable;

final class BelongsToManyPivotButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        BelongsToMany $field,
        bool $update = false,
        ?ActionButtonContract $button = null
    ): ActionButtonContract {
        /** @var ModelResource $resource */
        $resource = $field->getResource();
        /** @var ?CrudResourceContract $parentResource */
        $parentResource = $field->getNowOnResource() ?? moonshineRequest()->getResource();
        $parentPage = $field->getNowOnPage() ?? moonshineRequest()->getPage();
        $itemID = data_get($field->getNowOnQueryParams(), 'resourceItem', moonshineRequest()->getItemID());

        $action = static fn (?Model $data) => $parentResource->getRoute(
            'belongs-to-many-pivot.form',
            $itemID,
            [
                'pageUri' => $parentPage->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $update ? $data?->getKey() : null,
            ]
        );

        $authorize = $update
            ? static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                && $resource->hasAction(Action::UPDATE)
                && $resource->setItem($item)->can(Ability::UPDATE)
            : static fn (): bool => $resource->hasAction(Action::CREATE)
                && $resource->can(Ability::CREATE);

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        return $actionButton
            ->canSee($authorize)
            ->async()
            ->inModal(
                title: static fn (): array|string => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                content: '',
                builder: static fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)->autoClose()
            )
            ->square($update)
            ->primary()
            ->icon($update ? 'pencil' : 'plus')
            ->name("belongs-to-many-pivot-{$field->getRelationName()}-" . ($update ? 'edit' : 'create'))
            ->withoutLoading();
    }

    /**
     * @throws Throwable
     */
    public static function delete(
        BelongsToMany $field,
        ?ActionButtonContract $button = null
    ): ActionButtonContract {
        /** @var ModelResource $resource */
        $resource = $field->getResource();
        /** @var ?CrudResourceContract $parentResource */
        $parentResource = $field->getNowOnResource() ?? moonshineRequest()->getResource();
        $parentPage = $field->getNowOnPage() ?? moonshineRequest()->getPage();
        $itemID = data_get($field->getNowOnQueryParams(), 'resourceItem', moonshineRequest()->getItemID());

        $tableName = $field->getTableComponentName();

        $url = static fn (?Model $data) => $parentResource->getRoute(
            'belongs-to-many-pivot.destroy',
            $itemID,
            [
                'pageUri' => $parentPage->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $data?->getKey(),
            ]
        );

        $actionButton = $button ?? ActionButton::make('');

        return $actionButton
            ->setUrl($url)
            ->canSee(
                static fn (mixed $item, ?DataWrapperContract $data): bool => $data?->getKey()
                    && $resource->hasAction(Action::DELETE)
                    && $resource->setItem($item)->can(Ability::DELETE)
            )
            ->async(
                method: HttpMethod::DELETE,
                events: [AlpineJs::event($field->getComponentEvent(), $tableName)]
            )
            ->withConfirm(
                title: __('moonshine::ui.delete'),
                content: __('moonshine::ui.confirm'),
                button: __('moonshine::ui.delete'),
            )
            ->square()
            ->error()
            ->icon('trash')
            ->name("belongs-to-many-pivot-{$field->getRelationName()}-delete")
            ->showInLine();
    }
}
