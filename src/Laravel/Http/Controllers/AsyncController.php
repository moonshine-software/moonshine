<?php

namespace MoonShine\Laravel\Http\Controllers;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Select;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AsyncController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function component(MoonShineRequest $request): Renderable|Closure|string
    {
        $page = $request->getPage();

        $component = $page->getComponents()->findByName(
            $request->getComponentName()
        );

        if (is_null($component)) {
            return '';
        }

        if ($component instanceof TableBuilder) {
            return $this->responseWithTable($component);
        }

        return $component->render();
    }

    /**
     * @throws Throwable
     */
    public function method(MoonShineRequest $request): Response
    {
        $toast = [
            'type' => 'info',
            'message' => $request->input('message', ''),
        ];

        try {
            $pageOrResource = $request->hasResource()
                ? $request->getResource()
                : $request->getPage();

            $result = $pageOrResource
                ?->{$request->input('method')}(
                    $request
                );

            $toast = $request->session()->get('toast', $toast);
        } catch (Throwable $e) {
            report($e);

            $result = $e;
        }

        $request->session()->forget('toast');

        if ($result instanceof JsonResponse) {
            return $result;
        }

        if ($result instanceof BinaryFileResponse || $result instanceof StreamedResponse) {
            return $result;
        }

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? ToastType::ERROR : ToastType::from($toast['type'])
        );
    }

    /**
     * @throws Throwable
     */
    public function reactive(MoonShineRequest $request): JsonResponse
    {
        $page = $request->getPage();

        $form = $page->getComponents()->findForm(
            $request->getComponentName()
        );

        if (is_null($form)) {
            return $this->json();
        }

        $fields = $form
            ->preparedFields()
            ->onlyFields()
            ->reactiveFields();

        $values = $request->collect('values')->map(static function ($value, $column) use ($fields) {
            $field = $fields->findByColumn($column);

            if ($field instanceof Select) {
                return data_get($value, 'value', $value);
            }

            if ($field instanceof BelongsTo) {
                return [
                    $field->getRelatedModel()?->getKeyName() ?? 'id' => data_get($value, 'value', $value),
                ];
            }

            return $value;
        });

        $fields->fill($values->toArray());

        foreach ($fields as $field) {
            $fields = $field->reactiveCallback(
                $fields,
                data_get($values, $field->getColumn()),
                $values->toArray(),
            );
        }

        $values = $fields
            ->mapWithKeys(static fn (Field $field): array => [$field->getColumn() => $field->getValue()]);

        $fields = $fields->mapWithKeys(
            static fn (Field $field): array => [$field->getColumn() => (string) FieldsGroup::make([$field])->render()]
        );

        return $this->json(data: [
            'form' => $form,
            'fields' => $fields,
            'values' => $values,
        ]);
    }
}
