<?php

declare(strict_types=1);

namespace MoonShine\Support;

use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;

final readonly class AlpineJs
{
    public const EVENT_SEPARATOR = ':';

    public const EVENT_PARAMS_SEPARATOR = '|';

    public const EVENT_PARAM_SEPARATOR = ';';

    public static function event(string|JsEvent $event, ?string $name = null, array|EventParams $params = []): string
    {
        $event = \is_string($event) ? $event : $event->value;

        if (! \is_null($name)) {
            $event .= self::EVENT_SEPARATOR . $name;
        }

        $event = self::prepareEvents($event);

        if ($params instanceof EventParams) {
            $params = $params->toArray();
        }

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
        array|EventParams $params = []
    ): string {
        $event = \is_string($event) ? $event : $event->value;
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
        return value($condition) ?? false
            ? self::eventBlade($event, $name, $call)
            : '';
    }

    public static function asyncUrlDataAttributes(
        HttpMethod $method = HttpMethod::GET,
        string|array $events = [],
        null|string|array $selector = null,
        ?AsyncCallback $callback = null
    ): array {
        return array_filter([
            'data-async-events' => self::prepareEvents($events),
            'data-async-selector' => \is_array($selector) ? implode(',', $selector) : $selector,
            'data-async-response-handler' => $callback?->getResponseHandler(),
            'data-async-before-request' => $callback?->getBeforeRequest(),
            'data-async-after-response' => $callback?->getAfterResponse(),
            'data-async-method' => $method->value,
        ]);
    }

    /**
     * @param  array<string, string> $selectors
     */
    public static function asyncSelectorsParamsAttributes(array $selectors): array
    {
        return array_filter([
            'data-async-with-params' => collect($selectors)->map(static fn ($value, $key): string => is_numeric($key) ? $value : "$value/$key")->implode(','),
        ]);
    }

    public static function asyncFormDataAttributes(?string $selector = null): array
    {
        return [
            'data-async-form-data' => $selector ?? '',
        ];
    }

    public static function asyncWithQueryParamsAttributes(): array
    {
        return [
            'data-async-with-query-params' => true,
        ];
    }

    public static function onChangeSaveField(
        string $url,
        string $column,
        string $value = '',
        array $additionally = []
    ): array {
        return [
            '@change' => "saveField(`$url`, `$column`, $value);"
                . implode(';', array_filter($additionally)),
        ];
    }

    public static function dispatchEvents(string|array $events): string
    {
        $events = explode(',', self::prepareEvents($events));

        return implode(
            ';',
            array_map(static fn (string $event): string => "\$dispatch('$event')", $events)
        );
    }

    public static function prepareEvents(string|array $events): string
    {
        if (\is_array($events)) {
            return collect($events)
                ->map(static fn ($value): string => (string) str($value)->lower()->squish())
                ->filter()
                ->implode(',');
        }

        return strtolower($events);
    }
}
