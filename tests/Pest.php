<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\TestCase;

use function Pest\Laravel\actingAs;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

uses(TestCase::class)
    ->in(__DIR__);

uses(RefreshDatabase::class)
    ->in('Feature');

function fakeRequest(string $url = '/', string $method = 'GET', array $parameters = []): void
{
    app()->instance(
        'request',
        request()->create($url, $method, $parameters)
    );
}

function asAdmin(): TestCase
{
    return actingAs(MoonshineUser::query()->find(1), 'moonshine');
}

function exampleFields(): Fields
{
    return Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2'),
    ]);
}

function createRequest($method, $uri): Request
{
    $symfonyRequest = SymfonyRequest::create(
        $uri,
        $method,
    );

    return Request::createFromBase($symfonyRequest);
}

expect()->extend('isForbidden', function () {
    return expect($this->value->isForbidden())->toBeTrue();
});

expect()->extend('isSuccessful', function () {
    return expect($this->value->status())->toBeIn([200]);
});

expect()->extend('isRedirect', function () {
    return expect($this->value->status())->toBeIn([301, 302]);
});

expect()->extend('isSuccessfulOrRedirect', function () {
    return expect($this->value->status())->toBeIn([200, 301, 302]);
});

expect()->extend('see', function (string $value) {
    return expect($this->value->content())->toContain($value);
});

expect()->extend('hasFields', function (array $fields = null) {
    return expect($this->value)
        ->toBeCollection()
        ->toHaveCount($fields ? count($fields) : 0);
});
