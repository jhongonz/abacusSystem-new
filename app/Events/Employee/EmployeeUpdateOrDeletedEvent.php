<?php

namespace App\Events\Employee;

use Core\Employee\Domain\ValueObjects\EmployeeId;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeUpdateOrDeletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    private int $employeeId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function employeeId(): int
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
