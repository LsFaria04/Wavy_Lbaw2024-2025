<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostComment implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $comment;
    public $user;
    public $receiverid;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $comment
     * @param  mixed  $user
     * @param  int  $receiverid
     */
    public function __construct($comment, $user, $receiverid) {
        $this->comment = $comment;
        $this->user = $user;
        $this->receiverid = $receiverid;
        $this->message = $user->name . ' commented on your post: ' . $comment;  // Custom message
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array {
        // Broadcast to the private channel for the specific user (receiver)
        return [new PrivateChannel('user.' . $this->receiverid)];
    }

    /**
     * Get the name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs() {
        return 'notification-postcomment';  // The name of the event to bind to on the frontend
    }

}
