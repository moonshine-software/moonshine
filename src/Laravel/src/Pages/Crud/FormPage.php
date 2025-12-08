<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Contracts\Fields\HasModalModeContract;
use MoonShine\Crud\Contracts\Fields\HasTabModeContract;
use MoonShine\Crud\Pages\FormPage as CrudFormPage;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Traits\Page\OverrideCrudPage;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Divider;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use Throwable;

/**
 * @template TResource of CrudResource = \MoonShine\Laravel\Resources\ModelResource
 * @template TData of mixed = \Illuminate\Database\Eloquent\Model
 *
 * @extends CrudFormPage<TResource, TData, MoonShine, Fields>
 */
class FormPage extends CrudFormPage
{
    use OverrideCrudPage;

    protected function getFormAction(): string
    {
        $resource = $this->getResource();
        $item = $resource->getCastedData();

        return $resource->getRoute(
            $resource->isItemExists() ? 'crud.update' : 'crud.store',
            $item?->getKey(),
        );
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        $components = parent::bottomLayer();
        $item = $this->getItem();

        if (! $this->isItemExists()) {
            return $components;
        }

        $outsideFields = $this->getResource()->getOutsideFields()->formFields();

        if ($outsideFields->isEmpty()) {
            return array_merge($components, $this->getEmptyModals());
        }

        $tabs = [];

        $components[] = Divider::make();

        /** @var ModelRelationField $field */
        foreach ($outsideFields as $field) {
            $components[] = LineBreak::make();

            $fieldComponent = $field instanceof HasModalModeContract && $field->isModalMode()
                // With the modalMode, the field is already inside the fragment
                ? $field->fillCast(
                    $item,
                    $field->getResource()?->getCaster(),
                )
                : Fragment::make(array_filter([
                    $field instanceof HasTabModeContract && $field->isTabMode()
                        ? null
                        : Heading::make($field->getLabel()),

                    $field->fillCast(
                        $item,
                        $field->getResource()?->getCaster(),
                    ),
                ]))->canSee(static fn (): bool => $field->isSee())->name($field->getRelationName());

            if ($field instanceof HasTabModeContract && $field->isTabMode()) {
                $tabs[] = Tab::make($field->getLabel(), [
                    $fieldComponent,
                ])->canSee(static fn (): bool => $field->isSee());

                continue;
            }

            $components[] = $fieldComponent;
        }

        if ($tabs !== []) {
            $components[] = Tabs::make($tabs);
        }

        return array_merge($components, $this->getEmptyModals());
    }
}
