<?php

namespace MoonShine\Laravel\Pages;

use MoonShine\Laravel\Layouts\BlankLayout;
use MoonShine\UI\Components\FlexibleRender;

/**
 * @method static static make(int $code, string $message)
 */
class ErrorPage extends Page
{
    protected ?string $layout = BlankLayout::class;

    public function __construct(
        private readonly int $code,
        private readonly string $message
    ) {
        parent::__construct(
            (string) $this->code
        );
    }

    public function components(): array
    {
        $logo = moonshineAssets()->asset('vendor/moonshine/logo-small.svg');

        $code = $this->code;
        $message = $this->message;
        $backUrl = moonshineRouter()->getEndpoints()->home();

        return [
            FlexibleRender::make(
                static fn() => view('moonshine::errors.404'),
                ['code' => $code, 'message' => $message, 'logo' => $logo, 'backUrl' => $backUrl]
            ),
        ];
    }
}
