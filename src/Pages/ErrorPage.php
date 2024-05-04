<?php

namespace MoonShine\Pages;

use MoonShine\Components\FlexibleRender;
use MoonShine\Layouts\BlankLayout;

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
        $logo = asset(config('moonshine.logo') ?? 'vendor/moonshine/logo-small.svg');
        $code = $this->code;
        $message = $this->message;
        $backUrl = moonshineRouter()->home();

        return [
            FlexibleRender::make(
                view('moonshine::errors.404'),
                compact('code', 'message', 'logo', 'backUrl')
            ),
        ];
    }
}
