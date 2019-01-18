<?php

namespace App\Console\Commands;

use App\User;
use App\Server;
use Illuminate\Console\Command;
use App\Events\ServerWasCreated;

class MoreServersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:servers {--amount=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$users = User::query()->cursor();
		$amount = $this->option('amount');

		foreach ($users as $user) {
			$servers = factory(Server::class)
                ->times($amount)
                ->create([
                    'user_id' => $user->id,
                ]);

			$servers->each(function ($server) {
				event(new ServerWasCreated($server));
			});
		}
    }
}
