<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostComment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $postOwnerId;

    public function __construct($comment, $postOwnerId) {
        $this->comment = $comment;
        $this->postOwnerId = $postOwnerId;
    }

    public function broadcastOn(): array {
        return new PrivateChannel('user.' . $this->postOwnerId);
    }

    public function broadcastAs() {
        return 'notification-postcomment';
    }
}
