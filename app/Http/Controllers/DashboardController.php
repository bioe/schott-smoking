<?php

namespace App\Http\Controllers;

use App\Http\Plugins\ArduinoCall;
use App\Models\Station;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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

        return Inertia::render('Dashboard', [
            'stations' => $stations,
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
