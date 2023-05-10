<?php

uses()->group('controllers');

it('successful rendered', function () {
    asAdmin()
        ->get(route('moonshine.custom_page', 'profile'))
        ->assertOk()
        ->assertSeeText('Profile');
});
