<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Laravel\Pages\Dashboard;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HomeController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function __invoke(): Response|Renderable|string
    {
        $page = moonshineConfig()->getPage('dashboard', Dashboard::class);

        if ($page->isResponseModified() && ($response = $page->getModifiedResponse()) !== null) {
            return $response;
        }

        return (string) $page;
    }
}
