<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Pages;

use MoonShine\Laravel\Layouts\BlankLayout;
use MoonShine\UI\Components\FlexibleRender;

/**
 * @method static static make(int $code, string $message)
 */
class ErrorPage extends Page
{
    protected ?string $layout = BlankLayout::class;

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

    public function components(): array
    {
        $logo = moonshineAssets()->getAsset('vendor/moonshine/logo-small.svg');

        $code = $this->code;
        $message = $this->message;
        $backUrl = moonshineRouter()->getEndpoints()->home();

        return [
            FlexibleRender::make(
                static fn () => view('moonshine::errors.404'),
                ['code' => $code, 'message' => $message, 'logo' => $logo, 'backUrl' => $backUrl]
            ),
        ];
    }
}
