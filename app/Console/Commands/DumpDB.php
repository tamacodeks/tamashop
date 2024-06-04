<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DumpDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump the database in a period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('------------------------------------------------------');
        Log::info('Starting DB Backup....');
        $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
        Log::info('Backup db was finished');
        Log::info('------------------------------------------------------');
    }
}
