<?php

namespace App\Listeners;

use App\Events\ServerWasCreatedOnProvider;
use App\Events\ServerWasProvisioned;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProvisionServer implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  ServerWasCreatedOnProvider $event
     *
     * @return void
     */
    public function handle(ServerWasCreatedOnProvider $event)
    {
        sleep(3);
        // Provisions the server and marks it as ready.
        $event->server->markAsReady(date('c') . ' | Provisioning server');

        event(new ServerWasProvisioned($event->server));
    }
}
