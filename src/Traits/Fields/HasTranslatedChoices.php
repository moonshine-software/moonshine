<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\View\ComponentAttributeBag;

trait HasTranslatedChoices
{
    protected function performRender(): void
    {
        $this->customAttributes([
            'data-loading-text' => __('moonshine::ui.loading'),
            'data-no-results-text' => __('moonshine::ui.choices.no_results'),
            'data-no-choices-text' => __('moonshine::ui.choices.no_choices'),
            'data-item-select-text' => __('moonshine::ui.choices.item_select'),
            'data-unique-item-text' => __('moonshine::ui.choices.unique_item'),
            'data-custom-add-item-text' => __('moonshine::ui.choices.custom_add_item'),
            'data-add-item-text' => __('moonshine::ui.choices.add_item'),
            'data-max-item-text' => trans_choice(
                'moonshine::ui.choices.max_item',
                $this->customAttributes['data-max-item-count'] ?? 0
            ),
        ]);

        parent::performRender();
    }
}
