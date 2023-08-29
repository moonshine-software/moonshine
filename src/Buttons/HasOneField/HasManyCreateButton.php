<?php

namespace MoonShine\Buttons\HasOneField;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Relationships\HasMany;

final class HasManyCreateButton
{
    public static function for(HasMany $field, int $resourceId): ActionButton
    {
        $action = to_relation_route(
            'store',
            $resourceId,
        );

        $fields = $field->preparedFields();

        return ActionButton::make(__('moonshine::ui.add'), url: $action)
            ->customAttributes(['class' => 'btn btn-primary'])
            ->icon('heroicons.outline.plus')
            ->inModal(
                fn (): array|string|null => __('moonshine::ui.create'),
                fn (ActionButton $action): string => (string) FormBuilder::make($action->url())
                    ->precognitive()
                    ->name($field->getRelationName())
                    ->fields(
                        $fields
                        ->push(Hidden::make('_relation')->setValue($field->getRelationName()))
                        ->toArray()
                    )
            );
    }
}
