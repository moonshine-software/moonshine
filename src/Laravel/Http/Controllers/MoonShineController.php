<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller as BaseController;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Pages\QuickPage;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\Traits\Controller\InteractsWithAuth;
use MoonShine\Laravel\Traits\Controller\InteractsWithUI;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Components\Table\TableRow;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class MoonShineController extends BaseController
{
    use InteractsWithUI;
    use InteractsWithAuth;

    protected function json(
        string $message = '',
        array $data = [],
        string $redirect = null,
        ToastType $messageType = ToastType::SUCCESS
    ): JsonResponse {
        return MoonShineJsonResponse::make(data: $data)
            ->toast($message, $messageType)
            ->when(
                $redirect,
                static fn (MoonShineJsonResponse $response): MoonShineJsonResponse => $response->redirect($redirect)
            );
    }

    protected function view(string $path, array $data = []): PageContract
    {
        return QuickPage::make()->setContentView($path, $data);
    }

    /**
     * @throws Throwable
     */
    protected function reportAndResponse(bool $isAjax, Throwable $e, string $redirectRoute): Response
    {
        report_if(moonshine()->isProduction(), $e);

        $message = moonshine()->isProduction() ? __('moonshine::ui.saved_error') : $e->getMessage();
        $type = ToastType::ERROR;

        if($flash = session()->get('toast')) {
            session()->forget(['toast', '_flash.old', '_flash.new']);

            $message = $flash['message'] ?? $message;
        }

        if ($isAjax) {
            return $this->json(message: __($message), messageType: $type);
        }

        throw_if(! moonshine()->isProduction(), $e);

        $this->toast(__($message), $type);

        return redirect($redirectRoute)->withInput();
    }

    /**
     * @throws Throwable
     */
    protected function responseWithTable(TableBuilderContract $table): TableBuilderContract|TableRow|string
    {
        if (! request()->filled('_key')) {
            return $table;
        }

        $class = $table->hasCast()
            ? new ($table->getCast()->getClass())
            : null;

        if(! $class instanceof Model) {
            return $table->getRows()->first(
                static fn (TableRow $row): bool => $row->getKey() === request()->input('_key'),
            );
        }

        $item = $class::query()->find(request()->input('_key'));

        if (blank($item)) {
            return '';
        }

        return $table->items([
            $item,
        ])->getRows()->first();
    }
}
