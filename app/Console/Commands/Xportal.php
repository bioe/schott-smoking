<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\XportalEmployee;
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
        $emps = XportalEmployee::limit(10)->get();
        foreach ($emps as $e) {
            Employee::updateOrCreate(
                [
                    'card_id' => $e->Guid,
                    'origin_id' => $e->Id,
                ],
                [
                    'name' => $e->Mode,
                    'cost_center_id' => null
                ]
            );
        }
    }
}
