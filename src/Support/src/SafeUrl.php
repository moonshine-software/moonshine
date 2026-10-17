<?php

declare(strict_types=1);

namespace MoonShine\Support;

final class SafeUrl
{
    /**
     * @var list<string>
     */
    private const ALLOWED_SCHEMES = [
        'http',
        'https',
        'mailto',
        'tel',
    ];

    public static function sanitize(string $url): string
    {
        $normalized = \preg_replace('/[\x00-\x20\x7F-\x9F]+/u', '', $url);

        if ($normalized === null) {
            return '#';
        }

        $matches = [];
        \preg_match('/^([a-z][a-z0-9+.-]*):/i', $normalized, $matches);

        if (
            isset($matches[1])
            && ! \in_array(\strtolower($matches[1]), self::ALLOWED_SCHEMES, true)
        ) {
            return '#';
        }

        return $url;
    }
}
