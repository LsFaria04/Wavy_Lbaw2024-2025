<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class FollowNotification implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $receiverid;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $user
     * @param  int  $receiverid
     * @param  string  $type
     */
    public function __construct($user, $receiverid, $type = 'follow') {
        $this->user = $user;
        $this->receiverid = $receiverid;
        $this->type = $type;
    
        switch ($type) {
            case 'follow':
                $this->message = "{$user->username} started following you.";
                break;
            case 'follow-request':
                $this->message = "{$user->username} sent you a follow request.";
                break;
            case 'unfollowed':
                $this->message = "{$user->username} unfollowed you.";
                break;
            default:
                $this->message = "{$user->username} performed an action.";
                break;
        }
    }
    

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn() {
        Log::info("follow notification event");
        return new Channel('public-user.' . $this->receiverid);
    }

    /**
     * Get the name of the event to broadcast.
     *
     * @return string
     */
    public function broadcastAs() {
        return 'notification-follow';  // The name of the event to bind to on the frontend
    }
}