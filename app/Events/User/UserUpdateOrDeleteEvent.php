<?php

namespace App\Events\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdateOrDeleteEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $id)
    {
        $this->userId = $id;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
