<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use MoonShine\Crud\Contracts\Notifications\MoonShineNotificationContract;
use MoonShine\Crud\Contracts\Notifications\NotificationButtonContract;
use MoonShine\Support\Enums\Color;

uses()->group('notification-controller');

function fakeNotificationService(): MoonShineNotificationContract
{
    return new class () implements MoonShineNotificationContract {
        public int $readAllCalls = 0;

        public array $readIds = [];

        public function notify(
            string $message,
            ?NotificationButtonContract $button = null,
            array $ids = [],
            string|Color|null $color = null,
            ?string $icon = null
        ): void {
        }

        public function getAll(): Collection
        {
            return Collection::make();
        }

        public function readAll(): void
        {
            $this->readAllCalls++;
        }

        public function markAsRead(int|string $id): void
        {
            $this->readIds[] = $id;
        }

        public function getReadAllRoute(): string
        {
            return moonshineRouter()->to('notifications.readAll');
        }
    };
}

it('does not mark notifications as read via GET', function (): void {
    $notification = fakeNotificationService();
    $this->app->instance(MoonShineNotificationContract::class, $notification);

    asAdmin()
        ->get($this->moonshineCore->getRouter()->to('notifications.readAll'))
        ->assertNotFound();

    expect($notification->readAllCalls)
        ->toBe(0);
});

it('marks notifications as read via POST', function (): void {
    $notification = fakeNotificationService();
    $this->app->instance(MoonShineNotificationContract::class, $notification);

    asAdmin()
        ->post($this->moonshineCore->getRouter()->to('notifications.readAll'))
        ->assertRedirect();

    expect($notification->readAllCalls)
        ->toBe(1);
});
