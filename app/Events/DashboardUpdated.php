<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
class DashboardUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $dashboardData;
    public function __construct($dashboardData)
    {
        $this->dashboardData = $dashboardData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('dashboard')];
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->dashboardData // Correctly formatted payload
        ];
    }
}
