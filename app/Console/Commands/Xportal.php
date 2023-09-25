<?php

namespace App\Console\Commands;

use App\Http\Plugins\AdruinoCall;
use App\Models\Sensor;
use App\Models\Station;
use Exception;
use Illuminate\Console\Command;

class Xportal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xportal:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all xportal employee data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    }
}
