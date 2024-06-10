<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdatedOrDeletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $profileId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $id)
    {
        $this->profileId = $id;
    }

    public function profileId(): int
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
