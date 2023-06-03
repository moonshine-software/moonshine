<?php

use MoonShine\Notifications\MoonShineNotification;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses()->group('controllers');
uses()->group('notifications');

beforeEach(function (): void {
    //
});

it('read all notifications', function (): void {
    assertDatabaseCount('notifications', 0);

    MoonShineNotification::send('Message');

    assertDatabaseCount('notifications', 1);

    asAdmin()
        ->get(route('moonshine.notifications.readAll'));

    assertDatabaseHas('notifications', [
        'read_at' => now()->format('Y-m-d H:i:s'),
    ]);
});
