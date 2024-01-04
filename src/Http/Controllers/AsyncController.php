<?php

namespace MoonShine\Http\Controllers;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\FieldsGroup;
use MoonShine\Fields\Field;
use MoonShine\MoonShineRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class AsyncController extends MoonShineController
{
    /**
     * @throws Throwable
     * @see component
     * @deprecated will be removed in 3.0
     */
    public function table(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findTable(request('_component_name'));

        return $table ? $table->render() : '';
    }

    /**
     * @throws Throwable
     */
    public function component(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findByName(request('_component_name'));

        return $table ? $table->render() : '';
    }

    /**
     * @throws Throwable
     */
    public function method(MoonShineRequest $request): JsonResponse
    {
        $toast = [
            'type' => 'info',
            'message' => $request->get('message', ''),
        ];

        try {
            $pageOrResource = $request->hasResource()
                ? $request->getResource()
                : $request->getPage();

            $result = $pageOrResource
                ->{$request->get('method')}(
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

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? 'error' : $toast['type']
        );
    }

    public function reactive(MoonShineRequest $request): JsonResponse
    {
        $page = $request->getPage();

        $form = $page->getComponents()->findForm(
            $request->get('_component_name')
        );

        $values = $request->get('values', []);
        $fields = $form
            ->preparedFields()
            ->reactiveFields();

        $fields->fill($values);

        foreach ($fields as $field) {
            $fields = $field->reactiveCallback(
                $fields,
                data_get($values, $field->column()),
                $values,
            );
        }

        $values = $fields
            ->mapWithKeys(fn (Field $field) => [$field->column() => $field->value()]);

        $fields = $fields->mapWithKeys(function (Field $field) {
            return [$field->column() => (string) FieldsGroup::make([$field])->render()];
        });

        return $this->json('', [
            'form' => $form,
            'fields' => $fields,
            'values' => $values,
        ]);
    }
}
