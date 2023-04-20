<?php

declare(strict_types=1);

namespace MoonShine\Tests;

trait FakeRequests
{
    public function fakeRequest(string $url = '/', string $method = 'GET', array $parameters = []): void
    {
        app()->instance('request', request()->create($url, $method, $parameters));
    }
}
