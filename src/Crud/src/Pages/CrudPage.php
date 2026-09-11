<?php

declare(strict_types=1);

namespace MoonShine\Crud\Pages;

use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\ModalContract;
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

        return $this->getResourceOrFail()->isAsync();
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
     * @return list<ModalContract>
     */
    public function getEmptyModals(): array
    {
        $components = [];

        if ($this->getResourceOrFail()->isEditInModal()) {
            $modalName = $this->getResourceOrFail()->getUriKey() . '-edit-modal';

            $components[] = $this->getResourceOrFail()->resolveEditModal(
                Modal::make(
                    $this->getCore()->getTranslator()->getString('moonshine::ui.edit'),
                    components: [
                        Div::make()->customAttributes(['id' => $modalName]),
                    ],
                )->name($modalName)
            );
        }

        if ($this->getResourceOrFail()->isDetailInModal()) {
            $modalName = $this->getResourceOrFail()->getUriKey() . '-detail-modal';

            $components[] = $this->getResourceOrFail()->resolveDetailModal(
                Modal::make(
                    $this->getCore()->getTranslator()->getString('moonshine::ui.show'),
                    components: [
                        Div::make()->customAttributes(['id' => $modalName]),
                    ],
                )->name($modalName)
            );
        }

        return $components;
    }
}
