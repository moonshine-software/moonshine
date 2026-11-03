<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use MoonShine\Support\Attributes\AsyncMethod;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;

uses()->group('method-controller');

class AsyncMethodCsrfResource extends TestItemResource
{
    public static int $calls = 0;

    #[AsyncMethod]
    public function recordCall(): string
    {
        self::$calls++;

        return 'Method executed';
    }
}

class AsyncMethodCsrfExceptMiddleware extends VerifyCsrfToken
{
    protected $except = ['*/method/*'];
}

beforeEach(function (): void {
    // Exercise the real CSRF middleware instead of Laravel's test-environment bypass.
    $this->app['env'] = 'local';
    AsyncMethodCsrfResource::$calls = 0;
    $this->moonshineCore->resources([AsyncMethodCsrfResource::class]);
    $this->methodUrl = $this->moonshineCore->getRouter()->to('method', [
        'method' => 'recordCall',
        'resourceUri' => 'async-method-csrf-resource',
        'pageUri' => 'index-page',
    ]);
});

it('rejects an async method without a valid token before executing it', function (string $verb, ?string $token): void {
    asAdmin()->withSession(['_token' => 'valid-session-token'])
        ->call($verb, $this->methodUrl, server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'cross-site',
            ...($token === null ? [] : ['HTTP_X_CSRF_TOKEN' => $token]),
        ])
        ->assertStatus(419);

    expect(AsyncMethodCsrfResource::$calls)->toBe(0);
})->with(['GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE'])
    ->with([null, 'invalid-token']);

it('executes an async method with a valid token for every supported verb', function (string $verb): void {
    asAdmin()->withSession(['_token' => 'valid-session-token'])
        ->call($verb, $this->methodUrl, server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'cross-site',
            'HTTP_X_CSRF_TOKEN' => 'valid-session-token',
        ])
        ->assertOk();

    expect(AsyncMethodCsrfResource::$calls)->toBe(1);
})->with(['GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']);

it('ignores a method override in the query string of a read request', function (): void {
    asAdmin()->withSession(['_token' => 'valid-session-token'])
        ->call('GET', $this->methodUrl . '&_method=GET', server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'cross-site',
        ])
        ->assertStatus(419);

    expect(AsyncMethodCsrfResource::$calls)->toBe(0);
});

it('respects exclusions of the configured request-forgery middleware', function (): void {
    $this->app->bind(VerifyCsrfToken::class, AsyncMethodCsrfExceptMiddleware::class);

    asAdmin()->withSession(['_token' => 'valid-session-token'])
        ->call('GET', $this->methodUrl, server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'cross-site',
        ])
        ->assertOk();

    expect(AsyncMethodCsrfResource::$calls)->toBe(1);
});

it('leaves routes without request-forgery middleware alone', function (string $verb): void {
    asAdmin()->withoutMiddleware(VerifyCsrfToken::class)
        ->call($verb, $this->methodUrl, server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'cross-site',
        ])
        ->assertOk();

    expect(AsyncMethodCsrfResource::$calls)->toBe(1);
})->with(['GET', 'POST']);

it('accepts a same-origin read request without a token', function (): void {
    asAdmin()->withSession(['_token' => 'valid-session-token'])
        ->call('GET', $this->methodUrl, server: [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_SEC_FETCH_SITE' => 'same-origin',
        ])
        ->assertOk();

    expect(AsyncMethodCsrfResource::$calls)->toBe(1);
})->skip(! class_exists(PreventRequestForgery::class), 'Origin verification requires Laravel 13.');
