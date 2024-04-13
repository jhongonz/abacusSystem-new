<?php

namespace App\Events\User;

use Core\User\Domain\ValueObjects\UserId;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdateOrDeleteEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private UserId $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(UserId $id)
    {
        $this->userId = $id;
    }

    public function userId(): UserId
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
