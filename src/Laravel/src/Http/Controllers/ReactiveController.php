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
        $this->authorizeResourcePage($crudRequest);

        $page = $crudRequest->getPage();

        /** @var ?FormBuilderContract $form */
        $form = $page->getComponents()->findForm(
            $crudRequest->getComponentName(),
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

        /** @var \Illuminate\Support\Collection<string, mixed> $input */
        $input = $request->collect('values');
        $values = $input->map(
            function (mixed $value, string $column) use ($fields, &$casted, &$except) {
                $field = $fields->findByColumn($column);

                if (! $field instanceof HasReactivityContract) {
                    return $value;
                }

                return $field->prepareReactivityValue($value, $casted, $except);
            },
        );

        $fields->fill(
            $values->all(),
            $casted instanceof \Illuminate\Database\Eloquent\Model ? new ModelDataWrapper($casted->forceFill($values->except($except)->all())) : null,
        );

        /** @var array<string, mixed> $additionally */
        $additionally = $request->collect('additionally')->all();

        foreach ($fields as $field) {
            $fields = $field->formName($form->getName())->getReactiveCallback(
                $fields,
                data_get($values, $field->getColumn()),
                $values->all(),
                $additionally,
            );
        }

        $values = new \Illuminate\Support\Collection($fields->all())
            ->mapWithKeys(
                static fn (FieldContract $field): array => [$field->getColumn() => $field->getReactiveValue()],
            );

        $currentColumn = $request->input('current');

        $skipRender = static fn (FieldContract $field): bool
            => $field->isSilentReactive(data_get($values, $field->getColumn()), $values->all())
               || ($field->isSilentSelfReactive(
                   data_get($values, $field->getColumn()),
                   $values->all(),
               ) && $currentColumn === $field->getColumn());

        $fields = $fields->mapWithKeys(
            static fn (FieldContract $field): array => $skipRender($field)
                ? []
                : [
                    $field->getColumn() => (string)FieldsGroup::make([$field]),
                ],
        );

        return $this->json(data: [
            'form' => $form,
            'fields' => $fields,
            'values' => $values,
        ]);
    }
}
