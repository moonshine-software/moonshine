<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\Ability;
use MoonShine\Support\Enums\Action;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Modal;
use Throwable;

final class HasManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        HasMany $field,
        bool $update = false,
        ?ActionButtonContract $button = null,
    ): ActionButtonContract {
        /** @var ModelResource $resource */
        $resource = $field->getResourceOrFail()->stopGettingItemFromUrl();
        /** @var CrudResourceContract $parentResource */
        $parentResource = $field->getNowOnResource() ?? moonshineRequest()->getResourceOrFail();
        $parentPage = $field->getNowOnPage() ?? moonshineRequest()->getPage();
        /** @var int|string|null $itemID */
        $itemID = data_get($field->getNowOnQueryParams(), 'resourceItem', moonshineRequest()->getItemID());

        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (mixed $data) => $parentResource->getRoute(
            'has-many.form',
            $itemID,
            [
                'pageUri' => $parentPage->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => ($data instanceof Model ? $resource->getCaster()->cast($data)->getKey() : null),
            ]
        );

        if ($field->isWithoutModals()) {
            $action = static fn (mixed $data) => $resource->getFormPageUrl(($data instanceof Model ? $resource->getCaster()->cast($data)->getKey() : null));
        }

        $authorize = $update
            ? static fn (mixed $item, ?DataWrapperContract $data): bool => ($data instanceof Model ? $resource->getCaster()->cast($data)->getKey() : null)
                && $resource->hasAction(Action::UPDATE)
                && $item instanceof Model
                && $resource->setItem($item)->can(Ability::UPDATE)
            : static fn (): bool => $resource->hasAction(Action::CREATE)
                && $resource->can(Ability::CREATE);

        $actionButton = $button instanceof ActionButtonContract
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        $actionButton = $actionButton
            ->canSee($authorize)
            ->square($update)
            ->primary()
            ->icon($update ? 'pencil' : 'plus');

        if (! $field->isWithoutModals()) {
            $actionButton = $actionButton
                ->async()
                ->inModal(
                    title: static fn (): array|string => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                    content: '',
                    name: static fn (mixed $data): string => "has-many-modal-{$field->getResourceOrFail()->getUriKey()}-{$field->getRelationName()}-" . ($update ? ($data instanceof Model ? $resource->getCaster()->cast($data)->getKey() : null) : 'create'),
                    builder: static fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)
                );
        }

        return $actionButton
            ->name("has-many-{$field->getRelationName()}-button")
            ->withoutLoading()
        ;
    }
}
