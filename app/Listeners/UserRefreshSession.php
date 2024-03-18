<?php

namespace App\Listeners;

use App\Events\User\RefreshModulesSession;
use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserRefreshSession
{
    private ProfileManagementContract $profileService;
    
    /**
     * Create the event listener.
     */
    public function __construct(ProfileManagementContract $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Handle the event.
     */
    public function handle(RefreshModulesSession $event): void
    {
        /** @var Profile $profileSession */
        $profileSession = session('profile');
        $profile = $this->profileService->searchProfileById($profileSession->id());
        
        session()->forget('profile');
        session()->put('profile', $profile);
    }
}
