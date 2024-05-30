<?php

namespace MoonShine\Laravel\Pages\Crud;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Core\Exceptions\PageException;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Core\Pages\Page;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Components\Fragment;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\TableBuilder;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\Exceptions\MoonShineComponentException;
use Throwable;

/**
 * @method ModelResource getResource()
 * @extends Page<Fields>
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
     * @return MoonShineRenderable
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
     * @return MoonShineRenderable
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
     * @return MoonShineRenderable
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
     * @return MoonShineRenderable
     *@throws MoonShineComponentException
     * @throws PageException
     * @throws Throwable
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
