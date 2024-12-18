<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Follow implements ShouldBroadcast
{
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
        
        if ($type === 'follow') {
            $this->message = $user->name . ' started following you.';
        } elseif ($type === 'follow-request') {
            $this->message = $user->name . ' sent you a follow request.';
        }
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
        return 'notification-follow';  // The name of the event to bind to on the frontend
    }

}
