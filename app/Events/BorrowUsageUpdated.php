<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class BorrowUsageUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $userId;
    public $weeklyBorrows;
    public $tierLimit;
    public $atLimit;
    public $monthlyBorrows;
    public $monthlyLimit;
    public $atMonthlyLimit;

    public function __construct(int $userId, int $weeklyBorrows = 0, ?int $tierLimit = null)
    {
        $this->userId = $userId;
        $this->weeklyBorrows = $weeklyBorrows;
        $this->tierLimit = $tierLimit;
        $this->atLimit = $tierLimit ? ($weeklyBorrows >= $tierLimit) : false;
        $this->monthlyBorrows = 0;
        $this->monthlyLimit = null;
        $this->atMonthlyLimit = false;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('users.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'weeklyBorrows' => $this->weeklyBorrows,
            'tierLimit' => $this->tierLimit,
            'atLimit' => $this->atLimit,
            'monthlyBorrows' => $this->monthlyBorrows,
            'monthlyLimit' => $this->monthlyLimit,
            'atMonthlyLimit' => $this->atMonthlyLimit,
        ];
    }

    public function broadcastAs()
    {
        return 'BorrowUsageUpdated';
    }
}
