<?php

uses()->group('controllers');
uses()->group('auth');

it('view login page', function () {
    $this->get(route('moonshine.login'))
        ->assertOk()
        ->assertViewIs('moonshine::auth.login')
        ->assertSeeText('Welcome');
});

it('redirect to dashboard', function () {
    asAdmin()
        ->get(route('moonshine.login'))
        ->assertRedirect(route('moonshine.index'));
});

it('successful authenticated', function () {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'test']
    )->assertValid();
});

it('invalid credentials', function () {
    $this->post(
        route('moonshine.authenticate'),
        ['username' => $this->adminUser()->email, 'password' => 'invalid']
    )->assertInvalid(['username']);
});

it('logout', function () {
    asAdmin()
        ->get(route('moonshine.logout'))
        ->assertRedirect(route('moonshine.login'));
});
