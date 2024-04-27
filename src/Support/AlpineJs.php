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

        if (! is_null($name)) {
            $event .= self::EVENT_SEPARATOR . $name;
        }

        $event = self::prepareEvents($event);

        if ($params !== []) {
            $event .= self::EVENT_PARAMS_SEPARATOR
                . urldecode(
                    http_build_query($params, arg_separator: self::EVENT_PARAM_SEPARATOR)
                );
        }

        return $event;
    }

    public static function eventBlade(
        string|JsEvent $event,
        ?string $name = null,
        ?string $call = null,
        array $params = []
    ): string {
        $event = is_string($event) ? $event : $event->value;
        $name ??= 'default';
        $call = $call ? "='$call'" : '';


        return "@" . self::event($event, $name, $params) . '.window' . $call;
    }

    public static function eventBladeWhen(
        mixed $condition,
        string|JsEvent $event,
        ?string $name = null,
        ?string $call = null
    ): string {
        return Condition::boolean($condition, false)
            ? self::eventBlade($event, $name, $call)
            : '';
    }

    public static function asyncUrlDataAttributes(
        string $method = 'GET',
        string|array $events = [],
        ?string $selector = null,
        string|AsyncCallback|null $callback = null
    ): array {
        return array_filter([
            'data-async-events' => self::prepareEvents($events),
            'data-async-selector' => $selector,
            //TODO remove $callback string type in future version
            'data-async-callback' => $callback instanceof AsyncCallback ? $callback->success() : $callback,
            'data-async-before-function' => $callback instanceof AsyncCallback ? $callback->before() : null,
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

    public static function dispatchEvents(string|array $events): string
    {
        $events = explode(',', self::prepareEvents($events));

        return implode(
            ';',
            array_map(static fn ($event): string => "\$dispatch('$event')", $events)
        );
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
