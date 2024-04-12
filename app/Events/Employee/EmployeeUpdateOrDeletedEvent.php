<?php

namespace App\Events\Employee;

use Core\Employee\Domain\ValueObjects\EmployeeId;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeUpdateOrDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private EmployeeId $employeeId;

    /**
     * Create a new event instance.
     */
    public function __construct(EmployeeId $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function employeeId(): EmployeeId
    {
        return $this->employeeId;
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
