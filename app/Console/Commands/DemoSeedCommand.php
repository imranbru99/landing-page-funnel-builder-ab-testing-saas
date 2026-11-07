<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;

class DemoSeedCommand extends Command
{
    protected $signature = 'demo:seed';

    protected $description = 'Seed the Acme Growth Studio demo organization and sample data';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => DemoSeeder::class]);
        $this->info('Demo organization ready: demo@landingfunnel.test / password');

        return self::SUCCESS;
    }
}
