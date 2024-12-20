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
    public $userData;
    public $receiverid;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $user
     * @param  int  $receiverid
     * @param  string  $type
     */
    public function __construct(User $user, int $receiverid, string $type = 'follow') {
        $this->userData = $user->toArray();
        $this->receiverid = $receiverid;
        $this->type = $type;

        // Generate the message based on the user and type
        $this->message = $this->generateMessage($user->username, $type);
    }

    /**
     * Generate the notification message.
     *
     * @param  string  $username
     * @param  string  $type
     * @return string
     */
    private function generateMessage(string $username, string $type): string {

        switch ($type) {
            case 'follow':
                return "$username started following you.";
            case 'follow-request':
                return "$username sent you a follow request.";
            case 'unfollowed':
                return "$username unfollowed you.";
            default:
                return "$username performed an action.";
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): array {
        Log::info("follow notification event");
        return ['public-user.' . $this->receiverid];
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