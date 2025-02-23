<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostComment implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $comment;
    public $user;
    public $receiverid;
    public $isSubcomment;


    /**
     * Create a new event instance.
     *
     * @param  mixed  $comment
     * @param  mixed  $user
     * @param  int  $receiverid
     * @param  bool  $isSubcomment
     */
    public function __construct($comment, $user, $receiverid, $isSubcomment = false) {
        $this->comment = $comment;
        $this->user = $user;
        $this->receiverid = $receiverid;
        $this->isSubcomment = $isSubcomment;

        if ($isSubcomment) {
            $this->message = $user->username . ' replied to your comment';
        } else {
            $this->message = $user->username . ' commented on your post'; 
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn() {
        return new Channel('public-user.' . $this->receiverid);
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
