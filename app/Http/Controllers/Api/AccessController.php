<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EntryLog;
use App\Models\MessageLog;
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

        if ($request->has('card_id')) {
            $card_id = $request->card_id;
            $request_card_data = $request->card_id;
        } else if ($request->has('card_hex_last')) {
            $card_id = $this->decodeCard($station->code, $request->card_hex_last);
            $request_card_data = $request->card_hex_last;
        } else {
            $card_id = frontHexToNumber($request->card_hex);
            $request_card_data = $request->card_hex;
        }

        $employee = Employee::where('card_id', $card_id)->first();
        if ($employee == null) {
            \Log::channel('entry')->info('Employee ' . $request_card_data . "|" . $card_id . ' not found'); //Some detail msg cannot be shown
            return $this->logNRespone('Employee ' . $card_id . ' not found', $station->id, true);
        }

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
            if ($total_entry >= $station->max_pax)  return $this->logNRespone($employee->card_id . ', Current pax:' . $total_entry . ' Reach max capacity:' . $station->max_pax, $station->id, true);

            //A complete entry (got both enter + exit) prevent next entry all station until the delay is lift off
            $complete_entry = EntryLog::complete()->where('employee_id', $employee->id)
                ->where('maintenance', 0)
                ->orderBy('created_at', 'desc')->first();
            if (
                $complete_entry != null &&
                Carbon::now()->diffInSeconds($complete_entry->exit_time) < $complete_entry->disable_next_entry_seconds
            ) {
                return $this->logNRespone($employee->card_id . ', Last entry exit at ' . $complete_entry->exit_time . ', need to wait ' . $complete_entry->disable_next_entry_seconds, $station->id, true);
            }

            if (env('ENTRY_MODE') == "STRICT") {
                //Strict Check, need to have exit only can continue
                $exit = EntryLog::whereNull('exit_time')->where('employee_id', $employee->id)->where('maintenance', 0)->first();
                if ($exit != null) {
                    return $this->logNRespone($employee->card_id . ', Last entry enter at ' . $exit->enter_time . ', cannot enter again. You need to exit first.', $station->id, true);
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

        if ($request->has('card_id')) {
            $card_id = $request->card_id;
            $request_card_data = $request->card_id;
        } else if ($request->has('card_hex_last')) {
            $card_id = $this->decodeCard($station->code, $request->card_hex_last);
            $request_card_data = $request->card_hex_last;
        } else {
            $card_id = frontHexToNumber($request->card_hex);
            $request_card_data = $request->card_hex;
        }

        $employee = Employee::where('card_id', $card_id)->first();
        if ($employee == null) {
            \Log::channel('entry')->info('Employee ' . $request_card_data . "|" . $card_id . ' not found');
            return $this->logNRespone('Employee ' . $card_id . ' not found', $station->id, true);
        }

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
        if ($entry == null) return $this->logNRespone($employee->card_id . ', Missing Enter Entry Log', $station->id, true);

        //No Exit time only proceed to save
        if ($entry->exit_time == null) {
            $now = Carbon::now();
            $entry->exit_time = $now;
            //Find out how long they stay
            $entry->actual_stay_duration_seconds = $now->diffInSeconds($entry->enter_time);
            //Does the stay duration over the allowed stay duration from station setting
            if ($entry->actual_stay_duration_seconds > $entry->stay_duration_seconds) {
                $entry->overstay_seconds = $entry->actual_stay_duration_seconds - $entry->stay_duration_seconds;
            } else {
                $entry->overstay_seconds = 0;
            }
            $entry->save();
        }

        return response()->json(['door_open_seconds' => $station->door_open_seconds]);
    }

    public function logNRespone($msg, $station_id = null, $store = false)
    {
        if ($store) {
            MessageLog::create(
                [
                    'msg' => $msg,
                    'station_id' => $station_id
                ]
            );
        }
        \Log::channel('entry')->info($msg);
        return response()->json(['message' => $msg], 400);
    }

    /* To retry multiple time, due to incoming hex char might be in wrong position */
    public function decodeCard($station_code, $hex, $shift = 0)
    {
        $card_id = behindHexToNumber($hex, $shift);

        $employee = Employee::where('card_id', $card_id)->first();
        if ($employee == null) {
            \Log::channel('entry')->info($shift . '. Employee ' . $hex . "|" . $card_id . ' not found in ' . $station_code); //Some detail msg cannot be shown

            if ($shift <= 2) {
                //Retry
                return $this->decodeCard($station_code, $hex, $shift + 1);
            }
        }

        return $card_id;
    }
}
