<?php

declare(strict_types=1);

namespace MoonShine\Crud\Components\Layout;

use Illuminate\Support\Collection;
use MoonShine\Crud\Contracts\Notifications\MoonShineNotificationContract;
use MoonShine\Crud\Contracts\Notifications\NotificationItemContract;
use MoonShine\UI\Components\MoonShineComponent;

final class Notifications extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.notifications';

    protected array $translates = [
        'title' => 'moonshine::ui.notifications.title',
        'mark_as_read' => 'moonshine::ui.notifications.mark_as_read',
        'mark_as_read_all' => 'moonshine::ui.notifications.mark_as_read_all',
    ];

    private readonly MoonShineNotificationContract $notificationService;

    /**
     * @var Collection<array-key, NotificationItemContract>
     */
    public Collection $notifications;

    public string $readAllRoute = '';

    public function __construct()
    {
        parent::__construct();

        $this->notificationService = $this->getCore()
            ->getContainer(MoonShineNotificationContract::class);

        $this->notifications = $this->notificationService
            ->getAll();
    }

    protected function prepareBeforeRender(): void
    {
        $this->readAllRoute = $this->notificationService->getReadAllRoute();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'notifications' => $this->notifications,
            'readAllRoute' => $this->readAllRoute,
        ];
    }
}
