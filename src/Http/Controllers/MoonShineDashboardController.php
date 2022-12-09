<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

use Leeto\MoonShine\Dashboard\Dashboard;

use function view;

class MoonShineDashboardController extends BaseController
{
    public function index(): Factory|View|Application
    {
        return view('moonshine::dashboard', [
            'blocks' => app(Dashboard::class)->getBlocks()
        ]);
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

    public function autoUpdate(Request $request): array
    {
        $class = $request->get('model');
        $model = new $class();
        $item = $model->findOrFail($request->get('key'));

        $item->update([
            $request->get('field') => $request->boolean('value')
        ]);

        return $item->toArray();
    }
}
