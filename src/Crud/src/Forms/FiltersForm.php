<?php

declare(strict_types=1);

namespace MoonShine\Crud\Forms;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\FormContract;
use MoonShine\Contracts\UI\RelationFieldContract;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Traits\Makeable;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Hidden;
use RuntimeException;
use Stringable;
use Throwable;

/**
 * @method static static make(CrudResourceContract $resource)
 */
final readonly class FiltersForm implements FormContract
{
    use Makeable;

    public function __construct(
        private CrudResourceContract $resource,
        private CoreContract $core
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): FormBuilderContract
    {
        /** @var CrudResource $resource */
        $resource = $this->resource;
        $page = $resource->getIndexPage();

        if ($page === null) {
            throw new RuntimeException('Index page not defined');
        }

        $resource->setQueryParams(
            $this->core->getRequest()->getOnly($resource->getQueryParamsKeys()),
        );

        $values = $resource->getFilterParams();
        $filters = $resource->getFilters();

        $action = $page->isAsync() ? '#' : $this->getFormAction();

        foreach ($filters->onlyFields() as $filter) {
            if (! $filter instanceof RelationFieldContract) {
                $filter->fillData($values);
                data_forget($values, $filter->getColumn());
            }
        }

        $sort = $this->core->getRequest()->getScalar($resource->getQueryParamName('sort'));
        $queryTag = $this->core->getRequest()->getScalar($resource->getQueryParamName('query-tag'));

        return FormBuilder::make($action, FormMethod::GET)
            ->name('filters')
            ->customAttributes([
                'data-query-param-prefix' => $resource->getQueryParamPrefix(),
            ])
            ->fillCast($values, $resource->getCaster())
            ->fields(
                $filters
                    ->when(
                        $sort,
                        static fn ($fields): FieldsContract
                            => $fields
                            ->prepend(
                                Hidden::make(column: $resource->getQueryParamName('sort'))->setValue(
                                    $sort,
                                ),
                            ),
                    )
                    ->when(
                        $queryTag,
                        static fn ($fields): FieldsContract
                            => $fields
                            ->prepend(
                                Hidden::make(column: $resource->getQueryParamName('query-tag'))->setValue(
                                    $queryTag,
                                ),
                            ),
                    )
                    ->toArray(),
            )
            ->when($page->isAsync(), function (FormBuilderContract $form) use ($resource, $page): void {
                $events = [
                    $resource->getListEventName(),
                    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'filters-off-canvas'),
                ];

                $form->customAttributes([
                    '@submit.prevent' => "asyncFilters(
                        `" . AlpineJs::prepareEvents($events) . "`,
                        `_component_name,_token,_method`
                    )",
                ]);

                $form->buttons([
                    $this->getResetButton(
                        async: $page->isAsync(),
                        hide: true,
                        name: $resource->getQueryParamName('reset')
                    ),
                ]);
            })
            ->submit($this->core->getTranslator()->get('moonshine::ui.search'), ['class' => 'btn-primary'])
            ->when(
                $resource->getFilterParams() !== [],
                fn (FormBuilderContract $form): FormBuilderContract => $form->buttons([
                    $this->getResetButton(
                        name: $resource->getQueryParamName('reset')
                    ),
                ]),
            );
    }

    private function getResetButton(bool $async = false, bool $hide = false, string $name = 'reset'): ActionButton
    {
        $button = ActionButton::make(
            $this->core->getTranslator()->get('moonshine::ui.reset'),
            $this->getFormAction(query: [$name => true]),
        )
            ->secondary()
            ->showInLine()
            ->class('js-async-reset-button');

        if ($hide) {
            $button = $button->style('display: none');
        }

        if (! $async) {
            return $button;
        }

        return $button
            ->dispatchEvent([
                AlpineJs::event(
                    JsEvent::FORM_RESET,
                    'filters',
                ),
                AlpineJs::event(
                    JsEvent::FORM_SUBMIT,
                    'filters',
                ),
            ], withoutPayload: true);
    }

    /**
     * @param  array<string, mixed>  $query
     *
     */
    private function getFormAction(array $query = []): string
    {
        return Str::of($this->core->getRequest()->getUrl())->when(
            $query,
            static fn (Stringable $str): Stringable
                => $str
                ->append('?')
                ->append(Arr::query($query)),
        )->value();
    }
}
