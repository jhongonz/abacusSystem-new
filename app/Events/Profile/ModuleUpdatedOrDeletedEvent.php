<?php

namespace App\Events\Profile;

use Core\Profile\Domain\ValueObjects\ModuleId;
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

    public function moduleId(): ModuleId
    {
        return $this->moduleId;
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
