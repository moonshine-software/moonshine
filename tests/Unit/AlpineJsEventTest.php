<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ListRowEventType;
use MoonShine\Support\EventParams\ListRowEventParams;

uses()->group('events');

it('perform eventBlade with params', function () {
    $key = Str::uuid()->toString();
    $type = ListRowEventType::CHANGE;

    $event = AlpineJs::eventBlade(
        event: JsEvent::TABLE_ROW_UPDATED,
        name: 'my-table',
        params: ListRowEventParams::make($key, $type)
    );

    expect($event)
        ->toBe("@table_row_updated:my-table-$key|key~$key;type~$type->value;_delay~0.window");
});
