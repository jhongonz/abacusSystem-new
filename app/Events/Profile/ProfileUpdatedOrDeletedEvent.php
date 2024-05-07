<?php

namespace App\Events\Profile;

use Core\Profile\Domain\ValueObjects\ProfileId;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedOrDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private ProfileId $profileId;

    /**
     * Create a new event instance.
     */
    public function __construct(ProfileId $id)
    {
        $this->profileId = $id;
    }

    public function moduleId(): ProfileId
    {
        return $this->profileId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-profile'),
        ];
    }
}
