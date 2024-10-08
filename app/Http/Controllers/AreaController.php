<?php

namespace App\Http\Controllers;

use App\Models\Annoucement;
use App\Models\Banner;
use App\Models\EntryLog;
use App\Models\MessageLog;
use App\Models\Sensor;
use App\Models\Station;
use Carbon\Carbon;
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

        //Initialize list, because Splide need initialize at beginning
        $annoucement_list = Annoucement::where('active', true)->orderBy('updated_at', 'desc')->get();
        $banner_list = Banner::where('active', true)->orderBy('updated_at', 'desc')->get();

        return Inertia::render('SmokingArea', [
            'station_code' => $station->code,
            'station_last_update' => $station->updated_at,
            'max_pax' => $station->max_pax ?? 0,
            'warning_below_seconds' => $station->warning_below_seconds ?? 0,
            'annoucement_interval' => $station->annoucement_interval * 1000,
            'banner_interval' => $station->banner_interval * 1000,
            'annoucement_list' => $annoucement_list,
            'annoucement_last_update' => $annoucement_list->count() > 0 ? $annoucement_list[0]->updated_at : null,
            'banner_list' => $banner_list,
            'banner_last_update' =>  $banner_list->count() > 0 ? $banner_list[0]->updated_at : null,
            'polling_interval' => config('area.polling_interval')
        ]);
    }

    public function getLatest(Request $request, $code)
    {
        $station = Station::where('code', strtoupper($code))->first();
        if ($station == null) die("Station not found");

        $list = EntryLog::with('employee')->enterOnly()->where('station_id', $station->id)->orderBy('enter_time')->get();
        $announcement = Annoucement::where('active', true)->orderBy('updated_at', 'desc')->first();
        $banner = Banner::where('active', true)->orderBy('updated_at', 'desc')->first();
        $messages = MessageLog::where('station_id', $station->id)->whereBetween('created_at', [Carbon::now()->subMilliseconds(config('area.polling_interval', 0) * 3), Carbon::now()])->get(); //Show last x seconds messages

        $data['list'] = $list;
        $data['annoucement_last_update'] = $announcement ? $announcement->updated_at : null;
        $data['banner_last_update'] = $banner ? $banner->updated_at : null;
        $data['station_last_update'] = $station->updated_at;
        $data['air'] = Sensor::where('station_id', $station->id)->where('type', SENSOR_AIR)->orderBy('id', 'desc')->first(['value'])->value ?? 0;
        $data['temperature'] = Sensor::where('station_id', $station->id)->where('type', SENSOR_TEMP)->orderBy('id', 'desc')->first(['value'])->value ?? 0;
        $data['messages'] = $messages;

        return response()->json($data);
    }
}
