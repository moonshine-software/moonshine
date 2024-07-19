<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;

/**
 * @mixin RequestContract
 */
trait InteractsWithRequest
{
    public function getHost(): string
    {
        return $this->getRequest()->getUri()->getHost();
    }

    public function getPath(): string
    {
        $pattern = trim($this->getRequest()->getUri()->getPath(), '/');

        return $pattern === '' ? '/' : $pattern;
    }

    public function getUrl(): string
    {
        $url = $this->getRequest()->getUri()->getScheme() . '://' . $this->getHost();

        if($this->getRequest()->getUri()->getPort()) {
            $url .= ':' . $this->getRequest()->getUri()->getPort();
        }

        $url .= '/' . $this->getPath();

        return trim($url, '/');
    }

    public function urlIs(...$patterns): bool
    {
        $url = $this->getUrl();

        return collect($patterns)->contains(static fn ($pattern) => Str::is($pattern, $url));
    }

    public function getUrlWithQuery(array $query): string
    {
        $question = $this->getPath() === '/' ? '/?' : '?';

        return count($this->getRequest()->getQueryParams()) > 0
            ? $this->getUrl() . $question . Arr::query(array_merge($this->getRequest()->getQueryParams(), $query))
            : $this->getUrl() . $question . Arr::query($query);
    }
}
