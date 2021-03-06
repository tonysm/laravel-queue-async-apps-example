<?php

namespace App\Events;

use App\Server;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ServerWasProvisioned implements ShouldBroadcast
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

    public function broadcastOn()
    {
        return new PrivateChannel('servers.' . $this->server->user_id);
    }
}
