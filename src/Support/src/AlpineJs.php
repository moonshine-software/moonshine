<?php

declare(strict_types=1);

namespace MoonShine\Support;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\EventParams\EventParams;
use MoonShine\Support\EventParams\ListRowEventParams;

final readonly class AlpineJs
{
    public const EVENT_SEPARATOR = ':';

    public const EVENT_PARAMS_SEPARATOR = '|';

    public const EVENT_PARAM_SEPARATOR = ';';

    /**
     * @param  array<string, mixed>|EventParams  $params
     */
    public static function event(string|JsEvent $event, ?string $name = null, array|EventParams $params = []): string
    {
        $event = \is_string($event) ? $event : $event->value;

        if ($params instanceof ListRowEventParams) {
            $name = "$name-{$params->key}";
        }

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

        return str_replace('=', '~', $event);
    }

    /**
     * @param  array<string, mixed>|EventParams  $params
     */
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
        Closure|bool|null $condition,
        string|JsEvent $event,
        ?string $name = null,
        ?string $call = null
    ): string {
        return value($condition) ?? false
            ? self::eventBlade($event, $name, $call)
            : '';
    }

    /**
     * @param  string[]|string $events
     * @param  string[]|string|null $selector
     * @return array<string, mixed>
     */
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
     * @return array<string, string>
     */
    public static function asyncSelectorsParamsAttributes(array $selectors): array
    {
        return array_filter([
            'data-async-with-params' => (new Collection($selectors))->map(static fn ($value, $key): string => is_numeric($key) ? $value : "$value/$key")->implode(','),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function asyncWithQueryParamsAttributes(): array
    {
        return [
            'data-async-with-query-params' => true,
        ];
    }

    /**
     * @param  array<string, string>  $additionally
     *
     * @return array<string, string>
     */
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

    /**
     * @param  string|string[]  $events
     */
    public static function dispatchEvents(string|array $events): string
    {
        $events = explode(',', self::prepareEvents($events));

        return implode(
            ';',
            array_map(static fn (string $event): string => "\$dispatch('$event')", $events)
        );
    }

    /**
     * @param  string|string[]  $events
     */
    public static function prepareEvents(string|array $events): string
    {
        if (\is_array($events)) {
            return (new Collection($events))
                ->map(static fn ($value): string => (string) Str::of($value)->lower()->squish())
                ->filter()
                ->implode(',');
        }

        return strtolower($events);
    }
}
