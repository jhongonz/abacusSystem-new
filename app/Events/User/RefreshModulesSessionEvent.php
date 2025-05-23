<?php

namespace App\Events\User;

use Core\Profile\Domain\Contracts\ProfileManagementContract;
use Core\Profile\Domain\Profile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Session\Session;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshModulesSessionEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly ProfileManagementContract $profileManagement,
        private readonly Session $session
    ) {
    }

    public function profile(): Profile
    {
        /** @var Profile $profileSession */
        $profileSession = $this->session->get('profile');

        /** @var Profile $profile */
        $profile = $this->profileManagement->searchProfileById($profileSession->id()->value());

        return $profile;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-user'),
        ];
    }
}
