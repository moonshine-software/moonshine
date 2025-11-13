<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\Crud\JsonResponse;
use MoonShine\Laravel\TypeCasts\ModelDataWrapper;
use MoonShine\UI\Components\FieldsGroup;

final class ReactiveController extends MoonShineController
{
    public function __invoke(Request $request, CrudRequestContract $crudRequest): JsonResponse
    {
        $page = $crudRequest->getPage();

        /** @var ?FormBuilderContract $form */
        $form = $page->getComponents()->findForm(
            $crudRequest->getComponentName()
        );

        if (\is_null($form)) {
            return $this->json();
        }

        $fields = $form
            ->getPreparedFields()
            ->onlyFields()
            ->reactiveFields();

        $casted = null;
        $except = [];

        $values = $request->collect('values')->map(function (mixed $value, string $column) use ($fields, &$casted, &$except) {
            $field = $fields->findByColumn($column);

            if (! $field instanceof HasReactivityContract) {
                return $value;
            }

            return $field->prepareReactivityValue($value, $casted, $except);
        });

        $fields->fill(
            $values->toArray(),
            $casted ? new ModelDataWrapper($casted->forceFill($values->except($except)->toArray())) : null
        );

        foreach ($fields as $field) {
            $fields = $field->formName($form->getName())->getReactiveCallback(
                $fields,
                data_get($values, $field->getColumn()),
                $values->toArray(),
            );
        }

        $values = $fields
            ->mapWithKeys(static fn (FieldContract $field): array => [$field->getColumn() => $field->getReactiveValue()]);

        $fields = $fields->mapWithKeys(
            static fn (FieldContract $field): array => [$field->getColumn() => (string) FieldsGroup::make([$field])->render()]
        );

        return $this->json(data: [
            'form' => $form,
            'fields' => $fields,
            'values' => $values,
        ]);
    }
}
