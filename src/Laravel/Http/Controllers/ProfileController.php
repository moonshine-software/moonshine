<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Http\Requests\ProfileFormRequest;
use MoonShine\Laravel\Pages\ProfilePage;
use MoonShine\Support\Enums\ToastType;
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

        $success = $form->apply(
            static fn (Model $item) => $item->save(),
        );

        $message = $success ? __('moonshine::ui.saved') : __('moonshine::ui.saved_error');
        $type = $success ? ToastType::SUCCESS : ToastType::ERROR;

        if ($request->ajax()) {
            return $this->json(message: $message, messageType: $type);
        }

        $this->toast(
            __('moonshine::ui.saved'),
            $type
        );

        return back();
    }
}
