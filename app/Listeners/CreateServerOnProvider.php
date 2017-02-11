<?php

namespace App\Listeners;

use App\Events\ServerWasCreatedOnProvider;
use App\Events\ServerWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateServerOnProvider
{
    /**
     * Handle the event.
     *
     * @param  ServerWasCreated  $event
     * @return void
     */
    public function handle(ServerWasCreated $event)
    {
        // Trigger the creation on AWS and marks the server as provisioning.
        $event->server->markAsProvisioning(date('c') . ' | Triggered cloud provider to create the server');

        event(new ServerWasCreatedOnProvider($event->server));
    }
}
