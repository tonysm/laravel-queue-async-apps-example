<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 */
class Server extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsReady($logs)
    {
        $this->update([
            'logs' => $this->logs . PHP_EOL . $logs,
            'status' => 'ready',
        ]);
    }

    public function markAsProvisioning($logs)
    {
        $this->update([
            'logs' => $this->logs . PHP_EOL . $logs,
            'status' => 'provisioning',
        ]);
    }
}
