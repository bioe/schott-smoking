<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Station::create([
            'code' => "STATION1",
            'name' => "STATION1",
            'max_pax' => "10",
            'stay_duration_seconds' => "60",
            'warning_below_seconds' => "10",
            'disable_next_entry_seconds' => "0",
            'door_open_seconds' => "5",
            'annoucement_interval' => "10",
            'banner_interval' => "10",
            'ip' => "127.0.0.1",
            'active' => true
        ]);

        $employees = [
            [
                'card_id' => "123456789",
                'name' => "Emp 1",
                'staff_no' => '111'
            ],
            [
                'card_id' => "111111111",
                'name' => "Emp 2",
                'staff_no' => '222'
            ]
        ];
        foreach ($employees as $emp) {
            Employee::create($emp);
        }
    }
}
