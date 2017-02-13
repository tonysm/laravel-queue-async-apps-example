<?php

namespace App\Events;

use App\Server;
use Illuminate\Queue\SerializesModels;

class ServerWasCreated
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
}
