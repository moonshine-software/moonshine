<?php

namespace MoonShine\Modals;

class ConfirmActionModal extends Modal
{
    public function __construct(
        ?string $title = null,
        ?string $content = null
    ) {
        parent::__construct(
            $title ?? __('moonshine::ui.confirm'),
            $content ?? __('moonshine::ui.confirm_message')
        );
    }
}
