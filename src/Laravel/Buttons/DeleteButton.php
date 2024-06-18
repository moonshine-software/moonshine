<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Buttons;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;

final class DeleteButton
{
    public static function for(
        ModelResource $resource,
        ?string $componentName = null,
        string $redirectAfterDelete = '',
        bool $isAsync = true,
    ): ActionButton {
        $action = static fn (Model $data): string => $resource->getRoute(
            'crud.destroy',
            $data->getKey(),
            array_filter([
                ...$redirectAfterDelete
                    ? ['_redirect' => $redirectAfterDelete]
                    : [],
            ])
        );

        return ActionButton::make(
            '',
            url: $action
        )
            ->withConfirm(
                method: HttpMethod::DELETE,
                formBuilder: fn (FormBuilder $formBuilder, Model $item) => $formBuilder->when(
                    $isAsync || $resource->isAsync(),
                    fn (FormBuilder $form): FormBuilder => $form->async(
                        events: $resource->getListEventName(
                            $componentName ?? $resource->getListComponentName()
                        )
                    )
                ),
                name: fn (Model $data): string => "delete-modal-{$data->getKey()}"
            )
            ->canSee(
                fn (?Model $item): bool => ! is_null($item) && in_array('delete', $resource->getActiveActions())
                    && $resource->setItem($item)->can('delete')
            )
            ->error()
            ->icon('trash')
            ->showInLine();
    }
}
