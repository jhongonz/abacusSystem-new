<?php

namespace App\Events\Campus;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampusUpdatedOrDeletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $campusId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $campusId)
    {
        $this->campusId = $campusId;
    }

    public function campusId(): int
    {
        return $this->campusId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-campus'),
        ];
    }
}
