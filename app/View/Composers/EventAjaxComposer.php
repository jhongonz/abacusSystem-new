<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-12-19 21:20:36
 */

namespace App\View\Composers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventAjaxComposer
{
    public function __construct(
        private readonly ViewFactory $viewFactory,
        private readonly Request $request,
        private readonly Session $session
    ) {
    }

    public function compose(View $view): void
    {
        $isAjax = $this->request->ajax();

        if (null !== $this->session->get('user') && !$isAjax) {
            $this->viewFactory->composer('layouts.menu', MenuComposer::class);
        }

        $baseHome = (!$isAjax) ? 'layouts.home' : 'layouts.home-ajax';
        $view->with('layout', $baseHome);
    }
}
