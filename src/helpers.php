<?php

declare(strict_types=1);

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\AssetManager;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\MoonShineNotFoundException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Menu\MenuManager;
use MoonShine\MoonShine;
use MoonShine\MoonShineLayout;
use MoonShine\MoonShineRegister;
use MoonShine\MoonShineRequest;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Page;
use MoonShine\Support\SelectOptions;

if (! function_exists('moonshine')) {
    function moonshine(): MoonShine
    {
        return app(MoonShine::class);
    }
}

if (! function_exists('moonshineRegister')) {
    function moonshineRegister(): MoonShineRegister
    {
        return app(MoonShineRegister::class);
    }
}

if (! function_exists('moonshineRequest')) {
    function moonshineRequest(): MoonShineRequest
    {
        return app(MoonShineRequest::class);
    }
}

if (! function_exists('moonshineAssets')) {
    function moonshineAssets(): AssetManager
    {
        return app(AssetManager::class);
    }
}

if (! function_exists('moonshineMenu')) {
    function moonshineMenu(): MenuManager
    {
        return app(MenuManager::class);
    }
}

if (! function_exists('moonshineLayout')) {
    function moonshineLayout(): View
    {
        /* @var \MoonShine\Contracts\MoonShineLayoutContract $class */
        $class = config('moonshine.layout', MoonShineLayout::class);

        return $class::build()->render();
    }
}

if (! function_exists('form')) {
    function form(
        string $action = '',
        string $method = 'POST',
        Fields|array $fields = [],
        array $values = []
    ): FormBuilder {
        return FormBuilder::make($action, $method, $fields, $values);
    }
}

if (! function_exists('table')) {
    function table(
        Fields|array $fields = [],
        iterable $items = [],
        ?LengthAwarePaginator $paginator = null
    ): TableBuilder {
        return TableBuilder::make($fields, $items, $paginator);
    }
}

if (! function_exists('actionBtn')) {
    function actionBtn(
        Closure|string $label,
        Closure|string|null $url = null,
        mixed $item = null
    ): ActionButton {
        return ActionButton::make($label, $url, $item);
    }
}

if (! function_exists('findFieldApply')) {
    function findFieldApply(Field $field, string $type, string $for): ?ApplyContract
    {
        if($field->hasOnApply()) {
            return null;
        }

        $applyClass = moonshineRegister()
            ->{$type}()
            ->for($for)
            ->get($field::class);

        return
            ! is_null($applyClass)
            && class_exists($applyClass)
                ? new $applyClass()
                : null;
    }
}


if (! function_exists('formErrors')) {
    function formErrors(
        ViewErrorBag|bool $errors,
        ?string $name = null
    ): ViewErrorBag|MessageBag {
        if (! $errors) {
            return new ViewErrorBag();
        }

        if (is_null($name) || ! $errors->hasBag($name)) {
            return $errors;
        }

        return $errors->{$name};
    }
}

if (! function_exists('to_page')) {
    function to_page(
        string|Page|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        bool $redirect = false,
        ?string $fragment = null
    ): RedirectResponse|string {
        if ($fragment !== null && $fragment !== '') {
            $params += ['_fragment-load' => $fragment];
        }

        return MoonShineRouter::to_page(
            page: $page,
            resource: $resource,
            params: $params,
            redirect: $redirect
        );
    }
}

if (! function_exists('to_relation_route')) {
    function to_relation_route(
        string $action,
        int|string|null $resourceItem = null,
        ?string $relation = null,
        ?string $resourceUri = null,
        ?string $pageUri = null,
        ?string $parentField = null
    ): string {
        $data = [
            '_parent_field' => $parentField,
            '_relation' => $relation,
            'resourceItem' => $resourceItem,
        ];

        return MoonShineRouter::to("relation.$action", [
            'pageUri' => $pageUri ?? moonshineRequest()->getPageUri(),
            'resourceUri' => $resourceUri ?? moonshineRequest()->getResourceUri(),
            ...array_filter($data),
        ]);
    }
}

if (! function_exists('tableAsyncRoute')) {
    function tableAsyncRoute(string $componentName = 'index-table'): string
    {
        return route('moonshine.async.table', [
            '_component_name' => $componentName,
            '_parentId' => moonshineRequest()->getParentResourceId(),
            'resourceUri' => moonshineRequest()->getResourceUri(),
            'pageUri' => moonshineRequest()->getPageUri(),
            'filters' => moonshineRequest()->get('filters'),
            'query-tag' => moonshineRequest()->get('query-tag'),
            'search' => moonshineRequest()->get('search'),
        ]);
    }
}

if (! function_exists('updateRelationColumnRoute')) {
    function updateRelationColumnRoute(string $resourceUri, string $pageUri, string $relation): Closure
    {
        return fn ($item): string => MoonShineRouter::to(
            'column.relation.update-column',
            [
                'resourceItem' => $item->getKey(),
                'resourceUri' => $resourceUri,
                'pageUri' => $pageUri,
                '_relation' => $relation,
            ]
        );
    }
}

if (! function_exists('moonShineIndexRoute')) {
    function moonShineIndexRoute(): string
    {
        return config('moonshine.route.index', 'moonshine.index');
    }
}

if (! function_exists('is_closure')) {
    function is_closure(mixed $variable): bool
    {
        return $variable instanceof Closure;
    }
}

if (! function_exists('is_field')) {
    function is_field(mixed $variable): bool
    {
        return $variable instanceof Field;
    }
}

if (! function_exists('is_selected_option')) {
    function is_selected_option(mixed $current, string $value): bool
    {
        return SelectOptions::isSelected($current, $value);
    }
}

if (! function_exists('oops404')) {
    function oops404(): never
    {
        $handler = config(
            'moonshine.route.notFoundHandler',
            MoonShineNotFoundException::class
        );

        throw new $handler();
    }
}
