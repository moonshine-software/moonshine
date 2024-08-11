<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\TypeCasts\ModelCastedData;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Fields\Select;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ReactiveController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): JsonResponse
    {
        $page = $request->getPage();

        /** @var FormBuilderContract $form */
        $form = $page->getComponents()->findForm(
            $request->getComponentName()
        );

        if (is_null($form)) {
            return $this->json();
        }

        $fields = $form
            ->getPreparedFields()
            ->onlyFields()
            ->reactiveFields();

        $casted = null;

        $values = $request->collect('values')->map(static function ($value, $column) use ($fields, &$casted) {
            $field = $fields->findByColumn($column);

            if ($field instanceof Select) {
                $value = data_get($value, 'value', $value);
            }

            if ($field instanceof BelongsTo) {
                $value = data_get($value, 'value', $value);

                $casted = $field->getRelatedModel();
                $related = $field->getRelation()?->getRelated();

                $target = $related?->forceFill([
                    $related->getKeyName() => $value,
                ]);

                $casted?->setRelation($field->getRelationName(), $target);
            }

            return $value;
        });

        $fields->fill(
            $values->toArray(),
            $casted ? new ModelCastedData($casted) : null
        );

        foreach ($fields as $field) {
            $fields = $field->getReactiveCallback(
                $fields,
                data_get($values, $field->getColumn()),
                $values->toArray(),
            );
        }

        $values = $fields
            ->mapWithKeys(static fn (FieldContract $field): array => [$field->getColumn() => $field->getValue()]);

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
