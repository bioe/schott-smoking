<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EntryLog;
use App\Models\RawEntryLog;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    /* Payload
    * {station_code:String, card_hex:String}
    */
    public function enter_door(Request $request)
    {
        $station = Station::where('code', $request->station_code)->first();
        if ($station == null) return $this->logNRespone('Station ' . $request->station_code . ' not found');

        $card_id = hexToNumber($request->card_hex);
        $employee = Employee::where('card_id', $card_id)->first();
        if ($employee == null) return $this->logNRespone('Employee ' . $request->card_hex . "|" . $card_id . ' not found');

        //For record purpose only
        RawEntryLog::create([
            'employee_id' => $employee->id,
            'station_id' => $station->id,
            'card_hex' =>  $request->card_hex,
            'card_id' =>  $card_id,
            'enter_time' => Carbon::now(),
            'stay_duration_seconds' => $station->stay_duration_seconds,
            'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
            'maintenance' => $employee->maintenance
        ]);

        if ($employee->maintenance == 1) {
            //Skip all checks
        } else {
            //Check is max pax
            $total_entry = EntryLog::enterOnly()->where('station_id', $station->id)->count();
            if ($total_entry >= $station->max_pax)  return $this->logNRespone($employee->card_id . ', Current pax:' . $total_entry . ' Reach max capacity:' . $station->max_pax);

            //A complete entry (got both enter + exit) prevent next entry all station until the delay is lift off
            $complete_entry = EntryLog::complete()->where('employee_id', $employee->id)
                ->where('maintenance', 0)
                ->orderBy('created_at', 'desc')->first();
            if (
                $complete_entry != null &&
                Carbon::now()->diffInSeconds($complete_entry->exit_time) < $complete_entry->disable_next_entry_seconds
            ) {
                return $this->logNRespone($employee->card_id . ', Last entry exit at ' . $complete_entry->exit_time . ', need to wait ' . $complete_entry->disable_next_entry_seconds);
            }

            if (env('ENTRY_MODE') == "STRICT") {
                //Strict Check, need to have exit only can continue
                $exit = EntryLog::whereNull('exit_time')->where('employee_id', $employee->id)->where('maintenance', 0)->first();
                if ($exit != null) {
                    return $this->logNRespone($employee->card_id . ', Last entry enter at ' . $exit->enter_time . ', cannot enter again. You need to exit first.');
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
                'card_hex' =>  $request->card_hex,
                'card_id' =>  $card_id,
                'enter_time' => Carbon::now(),
                'stay_duration_seconds' => $station->stay_duration_seconds,
                'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
                'maintenance' => $employee->maintenance
            ]);
        }

        return response()->json(['door_open_seconds' => $station->door_open_seconds]);
    }

    /* Payload
    * {station_code:String, card_hex:String}
    */
    public function exit_door(Request $request)
    {
        $station = Station::where('code', $request->station_code)->first();
        if ($station == null) return $this->logNRespone('Station not found');

        $card_id = hexToNumber($request->card_hex);
        $employee = Employee::where('card_id',  $card_id)->first();
        if ($employee == null) return $this->logNRespone('Employee ' .  $card_id . ' not found');

        //For record purpose only
        RawEntryLog::create([
            'employee_id' => $employee->id,
            'station_id' => $station->id,
            'card_hex' =>  $request->card_hex,
            'card_id' =>   $card_id,
            'exit_time' => Carbon::now(),
            'stay_duration_seconds' => $station->stay_duration_seconds,
            'disable_next_entry_seconds' => $station->disable_next_entry_seconds,
            'maintenance' => $employee->maintenance
        ]);

        $entry = EntryLog::where('employee_id', $employee->id)->where('station_id', $station->id)
            ->when(env('ENTRY_MODE') == "STRICT", function ($q) {
                $q->enterOnly();
            })
            ->orderBy('created_at', 'desc')->first();
        if ($entry == null) return $this->logNRespone($employee->card_id . ', Missing Enter Entry Log');

        //No Exit time only proceed to save
        if ($entry->exit_time == null) {
            $now = Carbon::now();
            $entry->exit_time = $now;
            //Find out how long they stay
            $entry->actual_stay_duration_seconds = $now->diffInSeconds($entry->enter_time);
            //Does the stay duration over the allowed stay duration from station setting
            if ($entry->actual_stay_duration_seconds > $entry->stay_duration_seconds) {
                $entry->overstay_seconds = $entry->stay_duration_seconds - $entry->stay_duration_seconds;
            }
            $entry->save();
        }

        return response()->json(['door_open_seconds' => $station->door_open_seconds]);
    }

    public function logNRespone($msg)
    {
        \Log::channel('entry')->info($msg);
        return response()->json(['message' => $msg], 400);
    }
}
