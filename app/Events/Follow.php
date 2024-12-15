<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLike implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $receiverid;

    public function __construct($user, $receiverid) {
        $this->receiverid = $receiverid;
        $this->user = $user;
    }

    public function broadcastOn(): array {
        return new PrivateChannel('user.' . $this->receiverid);
    }

    public function broadcastAs() {
        return 'notification-follow';
    }
}
