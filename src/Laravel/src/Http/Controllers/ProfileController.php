<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Http\Requests\ProfileFormRequest;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ToastType;
use MoonShine\UI\Enums\HtmlMode;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function store(ProfileFormRequest $request): Response
    {
        $page = moonshineConfig()->getPage('profile', ProfilePage::class);
        $form = $page->getForm();

        $user = MoonShineAuth::getGuard()->user() ?? MoonShineAuth::getModel();

        $success = $form->apply(
            static fn (Model $item) => $item->save(),
        );

        $user->refresh();

        $message = $success ? __('moonshine::ui.saved') : __('moonshine::ui.saved_error');
        $type = $success ? ToastType::SUCCESS : ToastType::ERROR;

        if ($request->ajax()) {
            $data = [];

            $form
                ->getFields()
                ->onlyFields()
                ->fillCloned($user->toArray())
                ->refreshFields()
                ->each(function (FieldContract $field) use (&$data): void {
                    $data['htmlData'][] = [
                        'html' => (string) $field
                            ->resolveRefreshAfterApply()
                            ->render(),
                        'selector' => ".profile-form [data-field-selector='{$field->getNameDot()}']",
                        'htmlMode' => HtmlMode::OUTER_HTML->value,
                    ];
                });

            return $this
                ->json(message: $message, data: $data, messageType: $type)
                ->events([
                    AlpineJs::event(
                        JsEvent::FRAGMENT_UPDATED,
                        'profile',
                    ),
                    AlpineJs::event(
                        JsEvent::FRAGMENT_UPDATED,
                        'topbar-actions',
                    ),
                ]);
        }

        $this->toast(
            __('moonshine::ui.saved'),
            $type
        );

        return back();
    }
}
