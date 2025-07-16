<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Attributes\Layout;
use MoonShine\Laravel\Layouts\BlankLayout;
use MoonShine\MenuManager\Attributes\SkipMenu;
use MoonShine\UI\Components\FlexibleRender;

/**
 * @method static static make(int $code, string $message)
 */
#[SkipMenu]
#[Layout(BlankLayout::class)]
class ErrorPage extends Page
{
    private int $code;

    private string $message;

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function code(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
    {
        $logo = $this->getAssetManager()->getAsset('/vendor/moonshine/logo-small.svg');

        $backUrl = $this->getCore()->getRouter()->getEndpoints()->home();

        if ($resourceUri = $this->getCore()->getRouter()->extractResourceUri()) {
            $backUrl = $this->getCore()->getResources()->findByUri($resourceUri)?->getUrl() ?? $backUrl;
        }

        $code = $this->code;
        $message = $this->message;
        $backTitle = $this->getCore()
            ->getTranslator()
            ->get('moonshine::ui.back');

        /** @var view-string $view */
        $view = 'moonshine::errors.404';

        return [
            FlexibleRender::make(
                static fn () => view($view),
                ['code' => $code, 'message' => $message, 'logo' => $logo, 'backUrl' => $backUrl, 'backTitle' => $backTitle]
            ),
        ];
    }
}
