<?php

declare(strict_types=1);

namespace MoonShine\Support;

use MoonShine\Enums\JsEvent;

final class AlpineJs
{
    public const EVENT_SEPARATOR = '-';

    public const EVENT_PARAMS_SEPARATOR = ':';

    public const EVENT_PARAM_SEPARATOR = ';';

    public static function event(string|JsEvent $event, ?string $name = null, array $params = []): string
    {
        $event = is_string($event) ? $event : $event->value;

        if(! is_null($name)) {
            $event .= self::EVENT_SEPARATOR . $name;
        }

        if($params !== []) {
            $event .= self::EVENT_PARAMS_SEPARATOR
                . urldecode(
                    http_build_query($params, arg_separator: self::EVENT_PARAM_SEPARATOR)
                );
        }

        return $event;
    }

    public static function eventBlade(string|JsEvent $event, ?string $name = null, ?string $call = null): string
    {
        $event = is_string($event) ? $event : $event->value;
        $name ??= 'default';
        $call = $call ? "='$call'" : '';


        return "@" . self::event($event, $name) . '.window' . $call;
    }

    public static function eventBladeWhen(mixed $condition, string|JsEvent $event, ?string $name = null, ?string $call = null): string
    {
        return Condition::boolean($condition, false)
            ? self::eventBlade($event, $name, $call)
            : '';
    }

    public static function asyncUrlDataAttributes(
        string $method = 'GET',
        string|array $events = [],
        ?string $selector = null,
        ?string $callback = null,
    ): array {
        return array_filter([
            'data-async-events' => self::prepareEvents($events),
            'data-async-selector' => $selector,
            'data-async-callback' => $callback,
            'data-async-method' => $method,
        ]);
    }

    public static function requestWithFieldValue(
        string $url,
        string $column,
        string $value = '',
        array $additionally = []
    ): array {
        return [
            '@change' => "requestWithFieldValue(`$url`, `$column`, $value);"
                . implode(';', array_filter($additionally)),
        ];
    }

    public static function prepareEvents(string|array $events): string
    {
        if (is_array($events)) {
            return collect($events)
                ->map(fn ($value): string => (string) str($value)->lower()->squish())
                ->filter()
                ->implode(',');
        }

        return strtolower($events);
    }
}
