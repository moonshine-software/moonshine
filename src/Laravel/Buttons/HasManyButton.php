<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Collections\MoonShineRenderElements;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Hidden;
use Throwable;

final class HasManyButton
{
    /**
     * @throws Throwable
     */
    public static function for(
        HasMany $field,
        bool $update = false,
        ?ActionButton $button = null,
    ): ActionButton {
        /** @var ModelResource $resource */
        $resource = $field->getResource();
        $parentResource = moonshineRequest()->getResource();
        $parentPage = moonshineRequest()->getPage();

        if (! $resource->formPage()) {
            return ActionButton::emptyHidden();
        }

        $action = static fn (?Model $data) => $parentResource->route(
            'relation.has-many-form',
            moonshineRequest()->getItemID(),
            [
                'pageUri' => $parentPage?->uriKey(),
                '_relation' => $field->getRelationName(),
                '_key' => $data?->getKey(),
            ]
        );

        $isAsync = $resource->isAsync() || $field->isAsync();

        $authorize = $update
            ? fn (?Model $item): bool => ! is_null($item) && in_array('update', $resource->getActiveActions())
                && $resource->setItem($item)->can('update')
            : fn (?Model $item): bool => in_array('create', $resource->getActiveActions())
                && $resource->can('create');

        $actionButton = $button
            ? $button->setUrl($action)
            : ActionButton::make($update ? '' : __('moonshine::ui.add'), url: $action);

        return $actionButton
            ->canSee($authorize)
            ->inModal(
                title: fn (): array|string|null => __($update ? 'moonshine::ui.edit' : 'moonshine::ui.create'),
                content: '',
                async: $isAsync,
                wide: true,
            )
            ->primary()
            ->icon($update ? 'pencil' : 'plus');
    }
}
