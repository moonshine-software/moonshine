<?php

declare(strict_types=1);

namespace MoonShine\Support\Enums;

enum JsEvent: string
{
    case FRAGMENT_UPDATED = 'fragment_updated';

    case TABLE_UPDATED = 'table_updated';

    case TABLE_REINDEX = 'table_reindex';

    case TABLE_ROW_UPDATED = 'table_row_updated';

    case CARDS_UPDATED = 'cards_updated';

    case FORM_RESET = 'form_reset';

    case MODAL_TOGGLED = 'modal_toggled';

    case OFF_CANVAS_TOGGLED = 'off_canvas_toggled';

    case POPOVER_TOGGLED = 'popover_toggled';

    case TOAST = 'toast';
}
