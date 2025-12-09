<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages;

use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Crud\Collections\Fields;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Modal;

/**
 * @template  TResource of CrudResourceContract = \MoonShine\Crud\Resources\CrudResource
 * @template TCore of CoreContract = CoreContract
 * @template TFields of Fields = Fields
 *
 * @extends Page<TResource, TCore>
 * @implements CrudPageContract<TResource, TCore, TFields>
 */
abstract class CrudPage extends Page implements CrudPageContract
{
    protected bool $isAsync = true;

    public function isAsync(): bool
    {
        if ($this->isAsync === false) {
            return false;
        }

        return $this->getResource()->isAsync();
    }

    /**
     * @return list<ComponentContract>
     */
    protected function fields(): iterable
    {
        return [];
    }

    /**
     * @param  TFields  $fields
     *
     * @return TFields
     */
    protected function prepareFields(FieldsContract $fields): FieldsContract
    {
        return $fields;
    }

    /**
     * @return TFields
     */
    public function getFields(): FieldsContract
    {
        /** @var TFields $collection */
        $collection = $this->getCore()->getFieldsCollection($this->fields());

        return $this->prepareFields($collection);
    }

    /**
     * @return list<Modal>
     */
    public function getEmptyModals(): array
    {
        $components = [];

        if ($this->getResource()->isEditInModal()) {
            $components[] = $this->getResource()->resolveEditModal(
                Modal::make(
                    $this->getCore()->getTranslator()->get('moonshine::ui.edit'),
                    components: [
                        Div::make()->customAttributes(['id' => 'resource-edit-modal']),
                    ],
                )->name('resource-edit-modal')
            );
        }

        if ($this->getResource()->isDetailInModal()) {
            $components[] = $this->getResource()->resolveDetailModal(
                Modal::make(
                    $this->getCore()->getTranslator()->get('moonshine::ui.show'),
                    components: [
                        Div::make()->customAttributes(['id' => 'resource-detail-modal']),
                    ],
                )->name('resource-detail-modal')
            );
        }

        return $components;
    }
}
