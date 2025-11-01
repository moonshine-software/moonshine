<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages\PageComponents;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\PageComponents\DefaultFormContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;

final class DefaultForm implements DefaultFormContract
{
    use WithCore;

    public function __construct(CoreContract $core)
    {
        $this->setCore($core);
    }

    public function __invoke(
        FormPageContract $page,
        string $action,
        ?DataWrapperContract $item,
        FieldsContract $fields,
        bool $isAsync = true,
    ): FormBuilderContract {
        $resource = $page->getResource();

        return FormBuilder::make($action)
            ->cast($resource->getCaster())
            ->fill($item)
            ->fields([
                /** @phpstan-ignore argument.templateType */
                ...$fields
                    ->when(
                        ! \is_null($item),
                        static fn (Fields $fields): Fields
                            => $fields->push(
                                Hidden::make('_method')->setValue('PUT'),
                            ),
                    )
                    ->toArray(),
            ])
            ->when(
                ! $page->hasErrorsAbove(),
                fn (FormBuilderContract $form): FormBuilderContract => $form->errorsAbove($page->hasErrorsAbove()),
            )
            ->when(
                $isAsync,
                fn (FormBuilderContract $formBuilder): FormBuilderContract
                    => $formBuilder
                    ->async(
                        events: array_filter([
                            $resource->getListEventName(
                                $this->getCore()->getRequest()->getScalar('_component_name', 'default'),
                                $isAsync && $resource->isItemExists() ? array_filter([
                                    $resource->getQueryParamName('page') => $this->getCore()->getRequest()->getScalar($resource->getQueryParamName('page')),
                                    $resource->getQueryParamName('sort') => $this->getCore()->getRequest()->getScalar($resource->getQueryParamName('sort')),
                                ]) : [],
                            ),
                            ! $resource->isItemExists() && $resource->isCreateInModal()
                                ? AlpineJs::event(JsEvent::FORM_RESET, $resource->getUriKey())
                                : null,
                        ]),
                    ),
            )
            ->when(
                $page->isPrecognitive() || ($this->getCore()->getCrudRequest()->isFragmentLoad('crud-form') && ! $isAsync),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->precognitive(),
            )
            ->name($resource->getUriKey())
            ->submit(
                $this->getCore()->getTranslator()->get('moonshine::ui.save'),
                ['class' => 'btn-primary btn-lg'],
            )
            ->buttons($page->getFormButtons());
    }
}
