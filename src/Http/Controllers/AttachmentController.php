<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends MoonShineController
{
    public function __invoke(Request $request): array
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            return [
                'attachment' => Storage::url(
                    $file->store('attachments', 'public')
                ),
            ];
        }

        return [];
    }
}
