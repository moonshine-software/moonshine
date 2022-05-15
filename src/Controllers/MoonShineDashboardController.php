<?php

namespace Leeto\MoonShine\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MoonShineDashboardController extends BaseController
{
    public function index(): Factory|View|Application
    {
        return view('moonshine::index.index');
    }

    public function attachments(Request $request): array
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            return [
                'attachment' => Storage::url($file->store('attachments', 'public'))
            ];
        }

        return [];
    }
}
