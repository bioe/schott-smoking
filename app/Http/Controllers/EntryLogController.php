<?php

namespace App\Http\Controllers;

use App\Http\Requests\CostCenterUpdateRequest;
use App\Models\EntryLog;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EntryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'entrylog', [
            'keyword' => null,
            'start' => null,
            'end' => null,
            'station_id' => null,
            'overstay' => false
        ]);

        $list = EntryLog::with(['employee', 'station'])
            ->when(!empty($filters['keyword']), function ($q) use ($filters) {
                $q->whereHas('employee', function ($q) use ($filters) {
                    $q->where(function ($q) use ($filters) {
                        $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
                        $q->orWhere('card_id', 'like', '%' . $filters['keyword'] . '%');
                    });
                });
            })->when(!empty($filters['start']), function ($q)  use ($filters) {
                $q->startOfDay('enter_time', $filters['start']);
            })->when(!empty($filters['end']), function ($q)  use ($filters) {
                $q->endOfDay('exit_time', $filters['end']);
            })->byCostCenter(Auth::user())->filterSort($filters)->orderBy('created_at', 'desc')->paginate(config('forms.paginate'));


        $station_list = Station::all();

        return Inertia::render('EntryLog/Index', [
            'header' => EntryLog::header(),
            'filters' => $filters,
            'list' => $list,
            'station_list' => $station_list
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->edit(null);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CostCenterUpdateRequest $request)
    {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id = null)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterUpdateRequest $request, string $id = null)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryLog $entryLog)
    {
        $entryLog->delete();
        return Redirect::route('EntryLog.index')->with('message', 'Log deleted successfully');
    }
}
