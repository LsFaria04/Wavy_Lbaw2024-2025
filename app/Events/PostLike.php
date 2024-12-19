<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostLike implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $post_id;
    public $user;
    public $receiverid;

    /**
     * Create a new event instance.
     *
     * @param  int  $post_id
     * @param  mixed  $user
     * @param  int  $receiverid
     */
    public function __construct($post_id, $user, $receiverid) {
        $this->post_id = $post_id;
        $this->user = $user;
        $this->receiverid = $receiverid;
        $this->message = $user->name . ' liked your post ' . $post_id;  // Custom message
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array {
        Log::info("like notification");

        //public channel
        return ['public-user.'];

        // Broadcast to the private channel for the specific user (receiver)
        //return [new PrivateChannel('user.' . $this->receiverid)];
    }

    /**
     * Get the name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs() {
        return 'notification-postlike';  // The name of the event to bind to on the frontend
    }

}
