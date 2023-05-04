<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request;
use MoonShine\Tests\TestCase;

uses(TestCase::class)
    ->in(__DIR__);

uses(RefreshDatabase::class)
    ->in('Feature');

function fakeRequest(string $url = '/', string $method = 'GET', array $parameters = []): void
{
    app()->instance('request', request()->create($url, $method, $parameters));
}

function createRequest($method, $uri): Request
{
    $symfonyRequest = SymfonyRequest::create(
        $uri,
        $method,
    );

    return Request::createFromBase($symfonyRequest);
}

expect()->extend('isForbidden', function() {
    return expect($this->value->isForbidden())->toBeTrue();
});

expect()->extend('isSuccessful', function() {
    return expect($this->value->status())->toBeIn([200]);
});

expect()->extend('isRedirect', function() {
    return expect($this->value->status())->toBeIn([301, 302]);
});

expect()->extend('isSuccessfulOrRedirect', function() {
    return expect($this->value->status())->toBeIn([200, 301, 302]);
});

expect()->extend('see', function(string $value) {
    return expect($this->value->content())->toContain($value);
});
