<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FlushRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all data from redis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        Redis::command('flushdb');

        return CommandAlias::SUCCESS;
    }
}
