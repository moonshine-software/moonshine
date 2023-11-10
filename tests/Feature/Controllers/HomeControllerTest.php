<?php

uses()->group('home-controller');

it('home page', function () {
    asAdmin()->get('/admin')->assertOk();
});