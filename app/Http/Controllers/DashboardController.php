<?php

namespace App\Http\Controllers;

use App\Http\Plugins\ArduinoCall;
use App\Models\EntryLog;
use App\Models\Station;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stations = null;
        if (Auth::user()->remote_door_ids != null && !empty(Auth::user()->remote_door_ids)) {
            $stations = Station::where('active', true)->whereIn('id', Auth::user()->remote_door_ids)->get();
        }

        $todays = EntryLog::byCostCenter(Auth::user())->complete()
            ->where('created_at', '>=', Carbon::now()->startOfDay())
            ->where('maintenance', 0)->get();

        $total_entry = $todays->count();
        $total_duration = 0;
        $total_overstay_duration = 0;
        $employee_ids = [];
        foreach ($todays as $t) {
            $total_duration += $t->actual_stay_duration_seconds;
            $total_overstay_duration += $t->overstay_seconds;
            $employee_ids[] = $t->employee_id;
        }
        $total_employee = count(array_unique($employee_ids));

        //Chart
        $start = Carbon::now()->subDays(6)->startOfDay();
        $end = Carbon::now()->endOfDay();
        $periods = CarbonPeriod::create($start, $end);

        $bar_stay = [];
        $bar_overstay = [];
        $bar_label = [];
        foreach ($periods as $date) {
            $bar_stay[$date->format("d M")] = 0;
            $bar_overstay[$date->format("d M")] = 0;
        }
        $bar_label = array_keys($bar_stay);

        $sevenDays = EntryLog::byCostCenter(Auth::user())->complete()
            ->whereBetween('created_at', [$start, $end])
            ->where('maintenance', 0)->get();

        foreach ($sevenDays as $day) {
            if (isset($bar_stay[$day->created_at->format("d M")])) {
                $bar_stay[$day->created_at->format("d M")] += $day->actual_stay_duration_seconds;
            }

            if (isset($bar_overstay[$date->format("d M")]) && $day->overstay_seconds != null) {
                $bar_overstay[$day->created_at->format("d M")] += $day->overstay_seconds;
            }
        }

        //Covert to hours
        foreach ($bar_stay as $index => $bs) {
            $bar_stay[$index] = round($bs / 3600, 2);
        }

        foreach ($bar_overstay as $index => $bos) {
            $bar_overstay[$index] = round($bos / 3600, 2);
        }

        //Table
        $naughty_list = EntryLog::byCostCenter(Auth::user())->complete()
            ->with('employee')
            ->whereBetween('created_at', [$start, $end])
            ->where('maintenance', 0)
            ->where('overstay_seconds', '>', 0)
            ->groupBy('employee_id')
            ->selectRaw('employee_id, sum(overstay_seconds) as total_overstay')
            ->orderBy('total_overstay', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stations' => $stations,
            'total_entry' => $total_entry,
            'total_duration' => getHoursMinutes($total_duration),
            'total_overstay_duration' => getHoursMinutes($total_overstay_duration),
            'total_employee' => $total_employee,
            'bar_stay' => $bar_stay,
            'bar_overstay' => $bar_overstay,
            'bar_label' => $bar_label,
            'naughty_list' => $naughty_list
        ]);
    }

    public function postOpenDoor(Request $request)
    {
        $station = Station::find($request->station_id);
        if (empty($station->ip)) return response()->json(['message' => 'Station IP not setup.'], 400);

        try {
            $call = new ArduinoCall($station->ip);

            $direction_label = "Exit";
            if ($request->direction == "in") {
                $direction_label = "Enter";
                $call->postInUnlock($station->door_open_seconds);
            } else {
                $call->postOutUnlock($station->door_open_seconds);
            }

            return response()->json(['message' => 'Successfully Open ' . $direction_label . ' Door']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage() . "\nPlease try again."], 400);
        }
    }
}
