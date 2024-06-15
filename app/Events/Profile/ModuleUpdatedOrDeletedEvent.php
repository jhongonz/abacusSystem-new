<?php

namespace App\Events\Profile;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleUpdatedOrDeletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $moduleId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $moduleId)
    {
        $this->moduleId = $moduleId;
    }

    public function moduleId(): int
    {
        return $this->moduleId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-profile'),
        ];
    }
}
