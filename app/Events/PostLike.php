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
        $this->message = $user->username . ' liked your post';  // Custom message
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(){
        Log::info("like notification event");
        return new Channel('public-user.' . $this->receiverid);
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
