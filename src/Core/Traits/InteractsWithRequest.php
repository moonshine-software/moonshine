<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait InteractsWithRequest
{
    public function getHost(): string
    {
        return $this->request->getUri()->getHost();
    }

    public function getPath(): string
    {
        $pattern = trim($this->request->getUri()->getPath(), '/');

        return $pattern === '' ? '/' : $pattern;
    }

    public function getUrl(): string
    {
        $url = $this->request->getUri()->getScheme() . '://' . $this->getHost();

        if($this->request->getUri()->getPort()) {
            $url .= ':' . $this->request->getUri()->getPort();
        }

        $url .= '/' . $this->getPath();

        return trim($url, '/');
    }

    public function urlIs(...$patterns): bool
    {
        $url = $this->getUrl();

        return collect($patterns)->contains(fn ($pattern) => Str::is($pattern, $url));
    }

    public function getUrlWithQuery(array $query): string
    {
        $question = $this->getPath() === '/' ? '/?' : '?';

        return count($this->request->getQueryParams()) > 0
            ? $this->getUrl() . $question . Arr::query(array_merge($this->request->getQueryParams(), $query))
            : $this->getUrl() . $question . Arr::query($query);
    }
}
