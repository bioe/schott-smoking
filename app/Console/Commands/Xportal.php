<?php

namespace App\Console\Commands;

use App\Models\CostCenter;
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
        $this->comment('Start Query Employee');

        $emps = XportalEmployee::limit(10)->get();
        foreach ($emps as $e) {

            $cc = null;
            if (!empty(trim($e->Division))) {
                $cc = CostCenter::firstOrCreate(
                    ['code' => trim($e->Division)],
                );
            }

            //Incoming 0000000097064539, we only need last 10 digit
            $last10CardNo = substr(trim($e->CardNo), -10);
            Employee::updateOrCreate(
                [
                    'card_id' => $last10CardNo,
                ],
                [
                    'name' => trim($e->TName),
                    'cost_center_id' => $cc ? $cc->id : null
                ]
            );
        }

        $this->comment('Employee Import Complete');
    }
}
