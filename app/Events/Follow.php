<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Follow implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $user;
    public $receiverid;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User|array  $user
     * @param  int  $receiverid
     * @param  string  $type
     */
    public function __construct($user, $receiverid, $type = 'follow') {
        $this->user = $user;
        $this->receiverid = $receiverid;
        $this->type = $type;

        // Generate the message based on the user and type
        $this->message = $this->generateMessage($user, $type);
    }

    /**
     * Generate the notification message.
     *
     * @param  \App\Models\User|array  $user
     * @param  string  $type
     * @return string
     */
    private function generateMessage($user, $type): string {
        $name = is_array($user) ? $user['name'] : $user->name;

        switch ($type) {
            case 'follow':
                return "$name started following you.";
            case 'follow-request':
                return "$name sent you a follow request.";
            case 'unfollowed':
                return "$name unfollowed you.";
            default:
                return "$name performed an action.";
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
    public function broadcastAs(): string {
        return 'notification-follow';  // The name of the event to bind to on the frontend
    }
}
