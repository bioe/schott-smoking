<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EntryLog;
use App\Models\RawEntryLog;
use App\Models\Station;
use Carbon\Carbon;
use Dotenv\Parser\Entry;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function enter_door(Request $request)
    {
        $station = Station::where('code', $request->station_code)->first();
        if ($station == null) return response()->json(['message' => 'Station not found'], 400);

        $employee = Employee::where('card_id', $request->card_id)->first();
        if ($employee == null) return response()->json(['message' => 'Employee ' . $request->card_id . ' not found'], 400);

        //For record purpose only
        RawEntryLog::create([
            'employee_id' => $employee->id,
            'station_id' => $station->id,
            'card_id' =>  $request->card_id,
            'enter_time' => Carbon::now(),
            'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
            'maintenance' => $employee->maintenance
        ]);

        if ($employee->maintenance == 1) {
            //Skip all checks
        } else {
            //Check is max pax
            $total_entry = EntryLog::enterOnly()->where('station_id', $station->id)->count();
            if ($total_entry >= $station->max_pax)  return response()->json(['message' => 'Current pax:' . $total_entry . ' Reach max capacity:' . $station->max_pax], 400);

            //A complete entry (got both enter + exit) prevent next entry all station until the delay is lift off
            $complete_entry = EntryLog::complete()->where('employee_id', $employee->id)
                ->where('maintenance', 0)
                ->orderBy('created_at', 'desc')->first();
            if (
                $complete_entry != null &&
                Carbon::now()->diffInSeconds($complete_entry->exit_time) < $complete_entry->disable_next_entry_seconds
            ) {
                return response()->json(['message' => 'Last entry exit at ' . $complete_entry->exit_time . ', need to wait ' . $complete_entry->disable_next_entry_seconds], 400);
            }

            if (env('ENTRY_MODE') == "STRICT") {
                //Strict Check, need to have exit only can continue
                $exit = EntryLog::whereNull('exit_time')->where('employee_id', $employee->id)->where('maintenance', 0)->first();
                if ($exit != null) {
                    return response()->json(['message' => 'Last entry enter at ' . $exit->enter_time . ', cannot enter again. You need to exit first.'], 400);
                }
            }
        }

        //Prevent duplicate entry, example keep scan enter_door multiple time will not create new row, 
        //that will store in RawEntryLog 
        $entryLog = Entrylog::enterOnly()->where('employee_id', $employee->id)->where('station_id', $station->id)->first();
        if ($entryLog == null) {
            EntryLog::create([
                'employee_id' => $employee->id,
                'station_id' => $station->id,
                'card_id' =>  $request->card_id,
                'enter_time' => Carbon::now(),
                'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
                'maintenance' => $employee->maintenance
            ]);
        }

        return response()->json(['door_open_seconds' => $station->door_open_seconds]);
    }

    public function exit_door(Request $request)
    {
        $station = Station::where('code', $request->station_code)->first();
        if ($station == null) return response()->json(['message' => 'Station not found'], 400);

        $employee = Employee::where('card_id', $request->card_id)->first();
        if ($employee == null) return response()->json(['message' => 'Employee ' . $request->card_id . ' not found'], 400);

        //For record purpose only
        RawEntryLog::create([
            'employee_id' => $employee->id,
            'station_id' => $station->id,
            'card_id' =>  $request->card_id,
            'exit_time' => Carbon::now(),
            'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
            'maintenance' => $employee->maintenance
        ]);

        $entry = EntryLog::where('employee_id', $employee->id)->where('station_id', $station->id)
            ->when(env('ENTRY_MODE') == "STRICT", function ($q) {
                $q->enterOnly();
            })
            ->orderBy('created_at', 'desc')->first();
        if ($entry == null) return response()->json(['message' => 'Missing Enter Entry Log'], 400);

        //No Exit time save only proceed
        if ($entry->exit_time == null) {
            $now = Carbon::now();
            $entry->exit_time = $now;
            //Find out how long they stay
            $entry->stay_duration_seconds = $now->diffInSeconds($entry->enter_time);
            //Does the stay duration over the allowed stay duration from station setting
            if ($entry->stay_duration_seconds > $station->stay_duration_seconds) {
                $entry->overstay_seconds = $entry->stay_duration_seconds - $station->stay_duration_seconds;
            }
            $entry->save();
        }

        return response()->json(['door_open_seconds' => $station->door_open_seconds]);
    }
}
