<?php

namespace App\Events;

use App\Server;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ServerWasCreatedOnProvider implements ShouldBroadcast
{
    use SerializesModels;

    /**
     * @var \App\Server
     */
    public $server;

    /**
     * Create a new event instance.
     *
     * @param \App\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('servers.' . $this->server->user_id);
    }
}
