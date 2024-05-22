<?php

namespace MoonShine\Pages\Crud;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\Fragment;
use MoonShine\Components\Heading;
use MoonShine\Components\Layout\Box;
use MoonShine\Components\Layout\LineBreak;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Enums\PageType;
use MoonShine\Exceptions\MoonShineComponentException;
use MoonShine\Exceptions\PageException;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\MoonShineComponentAttributeBag;
use Throwable;

/**
 * @method ModelResource getResource()
 */
class DetailPage extends Page
{
    protected ?PageType $pageType = PageType::DETAIL;

    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        if (! is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        $breadcrumbs = parent::breadcrumbs();

        $breadcrumbs[$this->route()] = data_get($this->getResource()->getItem(), $this->getResource()->column());

        return $breadcrumbs;
    }

    /**
     * @throws ResourceException
     */
    protected function prepareBeforeRender(): void
    {
        abort_if(
            ! in_array('view', $this->getResource()->getActiveActions())
            || ! $this->getResource()->can('view'),
            403
        );

        parent::prepareBeforeRender();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    public function components(): array
    {
        $this->validateResource();
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            oops404();
        }

        return $this->getLayers();
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        $resource = $this->getResource();
        $item = $resource->getItem();

        return [
            Box::make([
                ...$this->getDetailComponents($item),
                LineBreak::make(),
                ...$this->getPageButtons($item),
            ]),
        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = [];
        $item = $this->getResource()->getItem();

        if (! $item?->exists) {
            return $components;
        }

        $outsideFields = $this->getResource()->getDetailFields(onlyOutside: true);

        if ($outsideFields->isNotEmpty()) {
            $components[] = LineBreak::make();

            /** @var ModelRelationField $field */
            foreach ($outsideFields as $field) {
                $field->resolveFill(
                    $item?->attributesToArray() ?? [],
                    $item
                );

                $components[] = LineBreak::make();

                $blocks = [
                    Heading::make($field->getLabel()),
                    $field,
                ];

                if ($field->toOne()) {
                    $field
                        ->withoutWrapper()
                        ->forcePreview();

                    $blocks = [
                        Box::make($field->getLabel(), [$field]),
                    ];
                }

                $components[] = Fragment::make($blocks)
                    ->name($field->getRelationName());
            }
        }

        return array_merge($components, $this->getResource()->getDetailPageComponents());
    }

    protected function getDetailComponent(?Model $item, Fields $fields): MoonShineRenderable
    {
        return TableBuilder::make($fields)
            ->cast($this->getResource()->getModelCast())
            ->items([$item])
            ->vertical()
            ->simple()
            ->preview()
            ->tdAttributes(fn (
                $data,
                int $row,
                int $cell,
                MoonShineComponentAttributeBag $attributes
            ): MoonShineComponentAttributeBag => $attributes->when(
                $cell === 0,
                fn (MoonShineComponentAttributeBag $attr): MoonShineComponentAttributeBag => $attr->merge([
                    'class' => 'font-semibold',
                    'width' => '20%',
                ])
            ));
    }

    /**
     * @throws Throwable
     * @throws MoonShineComponentException
     * @throws PageException
     * @return list<MoonShineRenderable>
     */
    protected function getDetailComponents(?Model $item): array
    {
        return [
            Fragment::make([
                $this->getDetailComponent($item, $this->getResource()->getDetailFields()),
            ])->name('crud-detail'),
        ];
    }

    protected function getPageButtons(?Model $item): array
    {
        return [
            ActionGroup::make($this->getResource()->getDetailItemButtons())
                ->setItem($item)
                ->customAttributes(['class' => 'justify-end']),
        ];
    }
}
