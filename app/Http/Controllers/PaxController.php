<?php

namespace App\Http\Controllers;


use App\Models\EntryLog;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['list'] = $this->numberOfPax();

        return Inertia::render('PaxStatus', [
            'data' => $data,
            'polling_interval' => config('area.polling_interval')
        ]);
    }

    public function getLatest(Request $request)
    {
        $data['list'] = $this->numberOfPax();
        return response()->json($data);
    }

    public function numberOfPax()
    {
        $stations = Station::where('active', true)->get();

        $list = [];
        foreach ($stations as $station) {
            $count = EntryLog::with('employee')->enterOnly()->where('station_id', $station->id)->orderBy('enter_time')->count();
            $list[] = ['station' => $station->code, 'total' => $count, 'max_pax' => $station->max_pax];
        }

        return $list;
    }
}
