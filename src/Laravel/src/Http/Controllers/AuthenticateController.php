<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Validation\ValidationException;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\Laravel\Http\Requests\LoginFormRequest;
use MoonShine\Laravel\Pages\LoginPage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateController extends MoonShineController
{
    /**
     * @param  ConfiguratorContract<MoonShineConfigurator>  $config
     */
    public function login(ConfiguratorContract $config, RouterContract $router): Renderable|Response|string
    {
        if ($this->auth()->check()) {
            return redirect(
                $router->getEndpoints()->home()
            );
        }

        $page = $config->getPage('login', LoginPage::class);

        if ($page->isResponseModified()) {
            return $page->getModifiedResponse();
        }

        return $page->render();
    }

    /**
     * @param  ConfiguratorContract<MoonShineConfigurator>  $config
     *
     * @throws ValidationException
     */
    public function authenticate(LoginFormRequest $request, ConfiguratorContract $config, RouterContract $router): Response
    {
        return $config->handleAuthenticate(function () use ($request, $config, $router) {
            if (filled($config->getAuthPipelines())) {
                $request = Pipeline::send($request)->through(
                    $config->getAuthPipelines()
                )->thenReturn();
            }

            if ($request instanceof JsonResponse) {
                return $request;
            }

            if ($request instanceof RedirectResponse) {
                return $request;
            }

            $request->authenticate();

            return redirect()->intended(
                $router->getEndpoints()->home()
            );
        });
    }

    /**
     * @param  ConfiguratorContract<MoonShineConfigurator>  $config
     */
    public function logout(Request $request, ConfiguratorContract $config, RouterContract $router): Response
    {
        return $config->handleLogout(function () use ($request, $router) {
            $this->auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect(
                $router->to('login')
            );
        });
    }
}
