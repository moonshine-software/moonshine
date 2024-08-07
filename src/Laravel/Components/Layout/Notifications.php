<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use Illuminate\Support\Collection;
use MoonShine\Laravel\Notifications\MoonShineNotificationContract;
use MoonShine\UI\Components\MoonShineComponent;

final class Notifications extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.notifications';

    protected array $translates = [
        'title' => 'moonshine::ui.notifications.title',
        'mark_as_read' => 'moonshine::ui.notifications.mark_as_read',
        'mark_as_read_all' => 'moonshine::ui.notifications.mark_as_read_all',
    ];

    public Collection $notifications;

    private readonly MoonShineNotificationContract $notificationService;

    public function __construct()
    {
        parent::__construct();


        $this->notificationService = $this->getCore()
            ->getContainer(MoonShineNotificationContract::class);

        $this->notifications = $this->notificationService
            ->getAll();
    }

    protected function viewData(): array
    {
        return [
            'readAllRoute' => $this->notificationService->getReadAllRoute(),
        ];
    }
}
