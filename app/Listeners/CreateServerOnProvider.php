<?php

namespace App\Listeners;

use App\Events\ServerWasCreatedOnProvider;
use App\Events\ServerWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateServerOnProvider implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  ServerWasCreated  $event
     * @return void
     */
    public function handle(ServerWasCreated $event)
    {
        sleep(3);

        // Trigger the creation on AWS and marks the server as provisioning.
        $event->server->markAsProvisioning(date('c') . ' | Triggered cloud provider to create the server');

        event(new ServerWasCreatedOnProvider($event->server));
    }
}
