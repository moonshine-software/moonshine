<?php

uses()->group('home-controller');

it('home page', static function () {
    asAdmin()->get('/admin')->assertOk();
});
