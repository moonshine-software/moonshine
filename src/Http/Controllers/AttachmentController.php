<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

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
