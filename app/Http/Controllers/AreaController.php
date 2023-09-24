<?php

namespace App\Http\Controllers;

use App\Models\Annoucement;
use App\Models\EntryLog;
use App\Models\Sensor;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $code)
    {
        $station = Station::where('code', strtoupper($code))->first();
        if ($station == null) die("Station not found");

        return Inertia::render('SmokingArea', [
            'station_code' => $station->code,
            'station_last_update' => $station->updated_at,
            'max_pax' => $station->max_pax ?? 0,
            'warning_below_seconds' => $station->warning_below_seconds ?? 0,
            'annoucement_interval' => $station->annoucement_interval * 1000,
            'banner_interval' => $station->banner_interval * 1000
        ]);
    }

    public function getLatest(Request $request, $code)
    {
        $station = Station::where('code', strtoupper($code))->first();
        if ($station == null) die("Station not found");

        $list = EntryLog::with('employee')->enterOnly()->where('station_id', $station->id)->orderBy('enter_time')->get();
        $announcement_list = Annoucement::where('active', true)->get();


        $data['list'] = $list;
        $data['annoucement_list'] = $announcement_list;
        $data['station_last_update'] = $station->updated_at;
        $data['air'] = Sensor::where('station_id', $station->id)->where('type', SENSOR_AIR)->orderBy('id', 'desc')->first(['value'])->value ?? 0;
        $data['temperature'] = Sensor::where('station_id', $station->id)->where('type', SENSOR_TEMP)->orderBy('id', 'desc')->first(['value'])->value ?? 0;

        return response()->json($data);
    }
}
