<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Leeto\MoonShine\Dashboard\Dashboard;

use function view;

class AttachmentController extends BaseController
{
    public function __invoke(Request $request): array
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            return [
                'attachment' => Storage::url($file->store('attachments', 'public')),
            ];
        }

        return [];
    }
}
