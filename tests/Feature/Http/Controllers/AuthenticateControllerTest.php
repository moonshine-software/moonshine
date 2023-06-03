<?php

uses()->group('controllers');
uses()->group('auth');

it('view login page', function (): void {
    $this->get(route('moonshine.login'))
        ->assertOk()
        ->assertViewIs('moonshine::auth.login')
        ->assertSeeText('Welcome');
});

it('redirect to dashboard', function (): void {
    asAdmin()
        ->get(route('moonshine.login'))
        ->assertRedirect(route('moonshine.index'));
});

it('successful authenticated', function (): void {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'test']
    )->assertValid();
});

it('invalid credentials', function (): void {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'invalid']
    )->assertInvalid(['username']);
});

it('logout', function (): void {
    asAdmin()
        ->get(route('moonshine.logout'))
        ->assertRedirect(route('moonshine.login'));
});
