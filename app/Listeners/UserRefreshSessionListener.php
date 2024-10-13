<?php

namespace App\Listeners;

use App\Events\User\RefreshModulesSessionEvent;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Contracts\Session\Session;

class UserRefreshSessionListener
{
    private ProfileManagementContract $profileService;
    private Session $session;

    /**
     * Create the event listener.
     */
    public function __construct(
        ProfileManagementContract $profileService,
        Session $session
    ) {
        $this->profileService = $profileService;
        $this->session = $session;
    }

    /**
     * Handle the event.
     */
    public function handle(RefreshModulesSessionEvent $event): void
    {
        /** @var Profile $profileSession */
        $profileSession = $this->session->get('profile');
        $profile = $this->profileService->searchProfileById($profileSession->id()->value());

        $this->session->forget('profile');
        $this->session->put('profile', $profile);
    }
}
