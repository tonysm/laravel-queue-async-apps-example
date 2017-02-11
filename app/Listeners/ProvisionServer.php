<?php

namespace App\Listeners;

use App\Events\ServerWasCreatedOnProvider;
use App\Events\ServerWasProvisioned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProvisionServer
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
        // Provisions the server and marks it as ready.
        $event->server->markAsReady(date('c') . ' | Provisioning server');

        event(new ServerWasProvisioned($event->server));
    }
}