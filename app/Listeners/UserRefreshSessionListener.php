<?php

namespace App\Listeners;

use App\Events\User\RefreshModulesSessionEvent;
use Illuminate\Contracts\Session\Session;

class UserRefreshSessionListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly Session $session,
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(RefreshModulesSessionEvent $event): void
    {
        $this->session->forget('profile');
        $this->session->put('profile', $event->profile());
    }
}
