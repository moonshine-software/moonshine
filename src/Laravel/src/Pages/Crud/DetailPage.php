<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages\Crud;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Crud\Buttons\DeleteButton;
use MoonShine\Crud\Components\Fragment;
use MoonShine\Crud\Contracts\Fields\HasTabModeContract;
use MoonShine\Crud\Pages\DetailPage as CrudDetailPage;
use MoonShine\Crud\Resources\CrudResource;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Traits\Page\OverrideCrudPage;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\LineBreak;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use Throwable;

/**
 * @template TResource of CrudResource = \MoonShine\Laravel\Resources\ModelResource
 *
 * @extends CrudDetailPage<TResource, MoonShine, Fields>
 */
class DetailPage extends CrudDetailPage
{
    use OverrideCrudPage;

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

        $outsideFields = $this->getResource()->getDetailFields(onlyOutside: true);

        $tabs = [];

        if ($outsideFields->isNotEmpty()) {
            $components[] = LineBreak::make();

            /** @var ModelRelationField $field */
            foreach ($outsideFields as $field) {
                $field->fillCast(
                    $item,
                    $field->getResource()?->getCaster(),
                );

                if ($field->isToOne()) {
                    $field
                        ->withoutWrapper()
                        ->previewMode();
                }

                $toOneRenderer = fn (ModelRelationField $field, ?string $redirectBack = null) => Box::make($field->getLabel(), array_filter([
                    $field instanceof HasOne
                        ? $field->modifyTable(
                            fn (TableBuilderContract $table): TableBuilderContract => $table->buttons([
                        $field->getFormModalButton(__('moonshine::ui.edit'), $redirectBack),

                        DeleteButton::for(
                            $field->getResource(),
                            $field->getRelationName(),
                            redirectAfterDelete: $this->getResource()->getDetailPageUrl(
                                $this->getResource()->getItemID(),
                            ),
                            modalName: "has-one-{$field->getRelationName()}",
                        ),
                    ]),
                        ) : $field,

                    ! $field->toValue() && $field instanceof HasOne
                        ? $field->getFormModalButton(__('moonshine::ui.add'), $redirectBack)
                        : null,
                ]));

                if ($field instanceof HasTabModeContract && $field->isTabMode()) {
                    $tabs[] = Tab::make($field->getLabel(), [
                        $field->isToOne() ? $toOneRenderer($field, $this->getResource()->getDetailPageUrl(
                            $this->getResource()->getItemID(),
                        )) : $field,
                    ])->canSee(static fn (): bool => $field->isSee());

                    continue;
                }

                $components[] = LineBreak::make();

                $blocks = $field->isToOne()
                    ? [$toOneRenderer($field)]
                    : [Heading::make($field->getLabel()), $field];

                $components[] = Fragment::make($blocks)
                    ->canSee(static fn (): bool => $field->isSee())
                    ->name($field->getRelationName());
            }
        }

        if ($tabs !== []) {
            $components[] = Tabs::make($tabs);
        }

        return array_merge($components, $this->getEmptyModals());
    }
}
