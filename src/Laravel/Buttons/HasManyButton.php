<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Enums\Ability;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Resources\ModelResource;
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
        $resource = $field->getResource();
        $parentResource = moonshineRequest()->getResource();
        $parentPage = moonshineRequest()->getPage();

        if (! $resource->getFormPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (?Model $data) => $parentResource->getRoute(
            'relation.has-many-form',
            moonshineRequest()->getItemID(),
            [
                'pageUri' => $parentPage?->getUriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $data?->getKey(),
            ]
        );

        if($field->isWithoutModals()) {
            $action = static fn (?Model $data) => $resource->getFormPageUrl($data);
        }

        $authorize = $update
            ? static fn (?Model $item): bool => ! is_null($item) && $resource->hasAction(Action::UPDATE)
                && $resource->setItem($item)->can(Ability::UPDATE)
            : static fn (?Model $item): bool => $resource->hasAction(Action::CREATE)
                && $resource->can(Ability::CREATE);

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        $actionButton = $actionButton
            ->canSee($authorize)
            ->async()
            ->primary()
            ->icon($update ? 'pencil' : 'plus');

        if(! $field->isWithoutModals()) {
            $actionButton = $actionButton->inModal(
                title: static fn (): array|string|null => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                content: '',
                name: static fn (?Model $data): string => "modal-has-many-{$field->getRelationName()}-" . ($update ? $data->getKey() : 'create'),
                builder: static fn (Modal $modal): Modal => $modal->wide()->closeOutside(false)
            );
        }

        return $actionButton->name("has-many-{$field->getRelationName()}-button");
    }
}
